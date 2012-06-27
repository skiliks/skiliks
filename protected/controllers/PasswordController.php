<?php


/**
 * Description of PasswordController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PasswordController extends AjaxController{
    
    /**
     * Восстановить пароль
     */
    public function actionRemember() {
        $email = Yii::app()->request->getParam('email', false);
        
        $result = array(
            'result' => 1
        );
        $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
