<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxController
 *
 * @author dorian
 */
class AjaxController extends CController
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR   = 0;
    
    public $is_test = false;

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
            header('Content-type: ' . $content_type . '; charset=UTF-8');
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Access-Control-Allow-Origin: *");
        }
        echo $body;

        Yii::app()->end();
    }

    /** 
     * @deprecated
     * @method void send JSON Writes JSON to output 
     */
    protected function sendJSON($data, $status = 200)
    {
        $this->_sendResponse($status, CJSON::encode($data));
    }
    
    /**
     * 
     */
    public function init()
    {
        parent::init();
        $app = Yii::app();
        if (in_array(Yii::app()->request->getParam('_lang'), ['en', 'ru']))
        {
            $app->language = Yii::app()->request->getParam('_lang');
            $app->session['_lang'] = $app->language;
        }
        else if (isset($app->session['_lang']))
        {
            $app->language = $app->session['_lang'];
        }
    }
    
    /**
     * @deprecated
     * @return integer || HttpResponce
     */
    public function getSessionId()
    {
        $sessionId = Yii::app()->request->getParam('sid', null);
        if (null === $sessionId) {            
            $this->returnErrorMessage('Не передан session ID.');
        }
        return $sessionId;
    }
    
    /**
     * @deprecated
     * @param integer $sessionId
     * @return integer || HttpResponse
     */
    public function getSimulationId($sessionId = null) 
    {
        $sessionId = (null == $sessionId) ? $this->getSessionId() : $sessionId;
            
        // stupidity, TODO: make normal sessions
        session_id($sessionId);
        
        $simulation = Simulations::model()->findByPk(Yii::app()->session['simulation']);
        
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
     * @deprecated
     * @return Simulations || HttpJsonResponce (Error)
     */
    public function getSimulationEntity()
    {
        $simulation = Simulations::model()->findByPk($this->getSimulationId());
        
        if (null !== $simulation) {
            return $simulation;
        } else {
            $this->returnErrorMessage(sprintf(
                'Simulation with id %s doesn`t exists in db.', 
                $this->getSimulationId()
            ));
        }
    }
    
    /**
     * @deprecated
     * @return Users || HttpJsonResponce (Error)
     */
    public function getCurrentUserId()
    {
        try {
            return SessionHelper::getUidBySid($this->getSessionId());
        } catch(\Exception $e) {
            $this->returnErrorMessage(
            $e->getMessage(),
            sprintf(
                'User with id %s doesn`t exists in db.', 
                $this->getSimulationId()
            ));
        }
    }


    /**
     * @deprecated
     * We handle Yii rroes and savethem to Yii.log. 
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
     * @return mixed array:
     *     array(
     *          'result'        => integer  // 0 - error || 1 - success
     *          'data'          => array,   // array with API responce necessary data
     *          'error_message' => string,  // message that will be displayed for user in error ('result' => 0) case
     *      )
     */
    public function processRequest()
    {
        try {
            $apiMethod = $this->initApiMethodObject();
            $apiMethod->validate();
            $data = $apiMethod->process();
            
            // all right way responce
            $this->sendJSON(array(
                'result' => self::STATUS_ERROR,
                'data'   => $data,
            ));
        } catch (FrontendNotificationException $e) {
            // expected error cases
            $this->sendJSON(array(
                'result'        => self::STATUS_SUCCESS,
                'error_message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            // unexpected error cases

            // log error {
            Yii::log($e->getMessage());
            Yii::log($e->getTraceAsString());
            // log error }
            
            // responce
            $this->sendJSON(array(
                'result' => self::STATUS_ERROR,
            ));
        }
    }
    
    /**
     * @return mixed object, instance of one from components/ApiMethods classes
     * 
     * @throws FrontendNotificationException
     */
    public function initApiMethodObject()
    {
        $className = sprintf (
            'api%s%s',
            ucfirst(Yii::app()->controller->id),
            ucfirst(Yii::app()->controller->action->id)
        );
        
        if (class_exists($className)) {
            return new $className();
        } else {
            Yii::log(sprintf(
                'Can`t find class %s to init API responce.',
                $className
            ));
            throw new FrontendNotificationException('Invalid API method name.');
        }
    }
    
    public function getAssetsUrl()
    {
        return Yii::app()->getAssetManager()
            ->publish(
                Yii::getPathOfAlias('application.assets'),
                false, 
                -1, 
                Yii::app()->params['assetsDebug']
            );
    }
}


