<?php

class SimulationBaseController extends CController {

    const STATUS_SUCCESS = 1;
    const STATUS_ERROR   = 0;

    public $is_test = false;

    /**
     * @var $user YumUser
     */
    public $user;

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
        if( null !== $uniqueId ) {
            if(is_array($data)) {
                $data['uniqueId'] = $uniqueId;
                $this->_sendResponse($status, CJSON::encode($data));
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
    public function getSimulationId($sessionId = null)
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