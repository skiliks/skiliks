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

        if (!$this->is_test)
            Yii::app()->end();
    }

    /** 
     * @method void sendJSON Writes JSON to output 
     */
    protected function sendJSON($data, $status = 200)
    {
        $this->_sendResponse($status, CJSON::encode($data));
    }
    
    /**
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
     * @param integer $sessionId
     * @return integer || HttpResponce
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
     * @param string $message
     * @return HttpJsonResponce
     */
    public function returnErrorMessage($sysLog, $message = 'Ошибка при обработке запроса.')
    {
        $errorId = date('Y-m-d H:i:s #'.rand(100, 999));
        
        // logging system error
        Yii::log(sprintf('Error No. %s. %s', $errorId, $sysLog));
        
        // display user error
        $this->_sendResponse(200, CJSON::encode(array(
            'result'  => self::STATUS_ERROR, 
            'message' => sprintf('Error No. %s. %s', $errorId, $message),
        )));
    }
}


