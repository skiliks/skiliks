<?php



/**
 * Кабинет пользователя
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserAccountController extends AjaxController{
    
    public function actionChangeEmail() {
        $sid = Yii::app()->request->getParam('sid', false);
        $email1 = Yii::app()->request->getParam('email1', false);
        
        $uid = SessionHelper::getUidBySid($sid);
        if (!$uid) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'cant find user'
            )));
        }
        
        $user = Users::model()->findByAttributes(array('id'=>$uid));
        if (!$user) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'cant find user'
            )));
        }
        
        $user->email = $email1;
        
        
        $result = array(
            'result' => (int)$user->save()
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
