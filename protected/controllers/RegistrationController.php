<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once('protected/controllers/AjaxController.php');

/**
 * Контроллер регистрации
 *
 * @author dorian
 */
class RegistrationController extends AjaxController{
    public function actionSave()
    {
        $login = $_POST['login'];
	$rows = array('result' => 1,
	"login"=>$login);
	$this->_sendResponse(200, CJSON::encode($rows));
    }
}

?>
