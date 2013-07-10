<?php

class SimulationBaseController extends CController {

    const STATUS_SUCCESS = 1;
    const STATUS_ERROR   = 0;

    public $is_test = false;

    /**
     * @var $user YumUser
     */
    public $user;

    protected $request_id = null;

    protected function beforeAction($action)
    {
        $this->saveLogServerRequest();

        return true;
    }

    protected function saveLogServerRequest() {

        $uid = Yii::app()->request->getParam('uniqueId');
        $request = Yii::app()->request->getParam('request');
        if(null === $uid) {
            throw new Exception("uid is not found");
        }
        $time = Yii::app()->request->getParam('time');
        if(null === $time) {
            throw new Exception("time is not found");
        }
        $data = $_POST;
        if(empty($data)) {
            throw new Exception("data is not found or empty");
        }
        $url = Yii::app()->request->url;
        $sim_url = Yii::app()->params['simulationStartUrl'];
        if($url !== $sim_url) {
            $simulation = $this->getSimulationEntity();
            if($request === 'repeat'){

                // log to site simulation actions
                SimulationService::logAboutSim($simulation, 'sim request tepeat');

                $log = LogServerRequest::model()->findByAttributes(['sim_id'=>$simulation->id, 'request_uid'=>$uid, 'is_processed'=>LogServerRequest::IS_PROCESSED_TRUE]);
                if(null === $log) {
                    /* @var $log LogServerRequest */
                    $log = new LogServerRequest("WithSimulation");
                    $log->sim_id = $simulation->id;
                    $log->backend_game_time = $simulation->getGameTime();
                }else{
                    $this->_sendResponse(200, $log->response_body);
                }

            }else{
                $log = new LogServerRequest("WithSimulation");
                $log->sim_id = $simulation->id;
                $log->backend_game_time = $simulation->getGameTime();

            }

        }else{
            $log = new LogServerRequest("WithoutSimulation");
        }
        $log->request_uid = $uid;
        $log->request_url = $url;
        $log->frontend_game_time = $time;
        $log->real_time = GameTime::setNowDateTime();
        $log->request_body = CJSON::encode($data);

        if(false === $log->save()){
            throw new Exception("Post data not save ");
        }
        $this->request_id = $log->id;
    }

    /**
     *
     * @param integer $status, 2xx, 3xx, 4xx, 5xx
     * @param string $body
     * @param string $content_type
     */
    protected function _sendResponse($status = 200, $body = '', $content_type = 'application/json')
    {
        if (!$this->is_test) {
            header("HTTP/1.0 200 OK");
            if ($status == 500) { header("HTTP/1.0 500 ERROR"); }
            header('Content-type: ' . $content_type . '; charset=UTF-8');
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Access-Control-Allow-Origin: *");
        }

        echo $body;

        Yii::app()->end();
    }

    /**
     * @method void send JSON Writes JSON to output
     */
    protected function sendJSON($data, $status = 200)
    {
        $uniqueId = Yii::app()->request->getParam('uniqueId', null);
        $simulation = $this->getSimulationEntity();
        if( null !== $uniqueId ) {
            if(is_array($data)) {
                $data['uniqueId'] = $uniqueId;
                /* @var $log LogServerRequest */
                $log = LogServerRequest::model()->findByAttributes(['sim_id'=>$simulation->id, 'request_uid' => $uniqueId]);
                if(null === $log){
                    throw new LogicException("log is not found by 'sim_id'=>{$simulation->id} and 'request_uid' => {$uniqueId}");
                }
                $json = CJSON::encode($data);
                $log->response_body = $json;
                $log->is_processed = LogServerRequest::IS_PROCESSED_TRUE;
                $log->update(['response_body', 'is_processed']);
                //sleep(30);
                $this->_sendResponse($status, $json);
            } else {
                throw new LogicException('$data should be an array');
            }
        } else {
            throw new LogicException("uniqueId not found");
        }
    }

    /**
     * We handle Yii errors and save them to Yii.log.
     * User see just standard notice
     *
     * @param string $message
     * @param string $sysLog
     * @return HttpJsonResponce
     */
    public function returnErrorMessage($sysLog, $message = 'Ошибка при обработке запроса.')
    {
        $errorId = date('Y-m-d H:i:s #'.rand(100, 999));

        // logging system error
        Yii::log(sprintf('Error No. %s. %s, (%s)', $errorId, $sysLog, $message));

        // display user error
        $this->_sendResponse(200, CJSON::encode(array(
            'result'  => self::STATUS_ERROR,
            'message' => sprintf('Error No. %s. %s', $errorId, $message),
        )));
    }

    /**
     * @deprecated
     * @param integer $sessionId
     * @return integer || HttpResponse
     */
    public function getSimulationId()
    {
        $simulation = Simulation::model()->findByPk(Yii::app()->session['simulation']);

        if (null === $simulation) {
            $this->returnErrorMessage(sprintf(
                    "Не могу получить симуляцию по ID %d",
                    Yii::app()->session['simulation']
                )
            );
        }

        return $simulation->id;
    }

    /**
     *
     * @return Simulation || HttpJsonResponce (Error)
     */
    public function getSimulationEntity()
    {
        $simulation = Simulation::model()->findByPk($this->getSimulationId());

        if (null !== $simulation) {
            return $simulation;
        } else {
            $this->returnErrorMessage(sprintf(
                'Simulation with id %s doesn`t exists in db.',
                $this->getSimulationId()
            ));
        }
    }
}