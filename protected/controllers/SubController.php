<?php

class SubController extends AjaxController
{
   /**
     * 
     */
    public function actionAdd()
    {
        $email = Yii::app()->request->getParam('email', false);

        $result = array(
            'result'  => 1,
            'message' => "Email {$email} has been successfully added!"
        );
        
        try {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $comand = Yii::app()->db->createCommand();
                $comand->insert( "emails_sub" , array(
                    'email'    => $email
                ));
            } else {
                throw new Exception("Invalid email - '{$email}'!");
            }


        } catch (CDbException $exc) {
            $result['result'] = 0;
            $result['message'] = "Email - {$email} has been already added before!";
        } catch (Exception $exc) {
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
        }
        
        $this->sendJSON($result);
        
        return;
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
