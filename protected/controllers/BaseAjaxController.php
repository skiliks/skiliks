<?php
/**
 * @author slavka
 */
class BaseAjaxController extends AjaxController
{
    /**
     * @return integer || HttpResponce
     */
    public function getSessionId()
    {
        $sessionId = Yii::app()->request->getParam('sid', null);
        if (null === $sid) {            
            $this->returnErrorMessage('Не передан session ID.');
        }
        $simId = SessionHelper::getSimIdBySid($sid);
    }
    
    /**
     * @param integer $sessionId
     * @return integer || HttpResponce
     */
    public function getSimulationId($sessionId = null) 
    {
        $sessionId = (null == $sessionId) ? $sessionId : $this->getSessionId();
            
        // stupidity, TODO: make normal sessions
        session_id($sessionId);
        
        $simulation = Simulations::model()->findByPk(Yii::app()->session['simulation']);
        
        if (null === $simulation) { 
            $this->returnErrorMessage(sprintf(
                "Не могу получить симуляцию по ID %d",
                Yii::app()->session['simulation']
            ));
        }
        
        return $simulation->id;
    }
    
    /**
     * @param string $message
     * @return HttpJsonResponce
     */
    public function returnErrorMessage($message)
    {
        $this->sendJSON(array(
            'result'  => 0, 
            'message' => $message,
        ));
    }
}

