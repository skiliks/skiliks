<?php



/**
 * Кабинет пользователя
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserAccountController extends AjaxController{
    
    public function actionChangeEmail() {
        $result = array(
            'result' => 1
        );
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionChangePassword() {
        $result = array(
            'result' => 1
        );
        $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
