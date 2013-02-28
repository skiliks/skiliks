<?php

/**
 * @author slavka
 */
class BaseApiMethod
{
    /**
     * private - to avoid posibility of dirty tricks ;)
     * @var string
     */
    private $sessionId = NULL;
    
    /**
     * private - to avoid posibility of dirty tricks ;)
     * @var Simulation
     */
    private $simulation = NULL;
    
    /**
     * private - to avoid posibility of dirty tricks ;)
     * @var Users
     */
    private $user = NULL;

    /**
     * Check all nessesary for process() HttpRequest data
     * and set it to XxxApiMethod attributes
     * 
     * @param HttpRequest $request
     * @throws FrontendNotificationException
     */
    public function validate(HttpRequest $request)
    {
        // set,check session ID
        $this->sessionId = $request->getParam('sid', NULL);
        if (NULL == $this->sessionId) {            
            throw new FrontendNotificationException('Invalid user-session.');
        }
        
        session_id($this->sessionId); // set session ID
        
        // set,check User
        $this->user = Users::model()->findByPk(Yii::app()->session['uid']);
        if (NULL == $this->user) {
            throw new FrontendNotificationException('Unexistent user.');
        }
        
        // set,check Simulation
        $this->simulation = Simulation::model()->findByPk(Yii::app()->session['simulation']);
        if (null == $this->simulation) { 
            throw new FrontendNotificationException('Unexistent simulation.');
        }
    }
    
    /**
     * Empty in base class
     */
    public function process()
    {
        return true;
    }
    
    /* -------------------------------------------------------------------------------------------------------------- */
    
    /**
     * @return Simulation
     */
    public function getSimulation()
    {
        return $this->simulation;
    }
    
    /**
     * @return integer
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
    
    /**
     * @return Users
     */
    public function getUser()
    {
        return $this->user;
    }    
}

