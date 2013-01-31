<?php

class SubController extends AjaxController
{
   /**
     * 
     */
    public function actionAdd()
    {
        Yii::app()->setLanguage('ru');
        $email = Yii::app()->request->getParam('email', false);

        $result = array(
            'result'  => 1,
            'message' => Yii::t('site', 'Email {email} has been successfully added!', ['{email}' => $email])
        );
        
        try {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $comand = Yii::app()->db->createCommand();
                $comand->insert( "emails_sub" , array(
                    'email'    => $email
                ));
            } else {
                throw new Exception(
                    Yii::t('site', "Invalid email - '{email}'!", ['{email}' => $email])
                );
            }


        } catch (CDbException $exc) {
            $result['result'] = 0;
            $result['message'] =  Yii::t('site', "Email - {email} has been already added before!", ['{email}' => $email]);
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
