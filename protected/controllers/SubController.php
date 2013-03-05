<?php

class SubController extends AjaxController
{
    /**
     *
     */
    public function actionAdd()
    {
        $email = Yii::app()->request->getParam('email', false);
        $result = UserService::addUserSubscription($email);

        $this->sendJSON($result);
    }
    
    /**
     * 
     */
    public function actionList() {
        
        $emails = Yii::app()->db->createCommand()
	    		->select( 'id, email' )
	    		->from( 'emails_sub' )
	    		->queryAll();
        echo 'ID EMAIL <br>';
        foreach ($emails as $email) {
            
        echo "{$email['id']} {$email['email']} <br>";
        }             
        
    }
}
