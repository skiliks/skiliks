<?php

class SubController extends AjaxController
{

    public function actionAdd()
    {
        $email = Yii::app()->request->getParam('email', false);
        //$email = 'list@i.ua';

        $result = array(
            'result'  => 1,
            'message' => "Ваш email - {$email} добавлен!"
        );
        
        try {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $comand = Yii::app()->db->createCommand();
                $comand->insert( "emails_sub" , array(
                    'email'    => $email
                ));
            } else {
                throw new Exception("Некорректный email - '{$email}'!");
            }


        } catch (CDbException $exc) {
            $result['result'] = 0;
            $result['message'] = "Email - {$email} ранее был добавлен!";
        } catch (Exception $exc) {
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
        }
        
        $this->sendJSON($result);
        
        return;
    }
}
