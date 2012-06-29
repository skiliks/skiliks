<?php



/**
 * Кабинет пользователя
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserAccountController extends AjaxController{
    
    protected function _getUser() {
        $sid = Yii::app()->request->getParam('sid', false);
        
        $uid = SessionHelper::getUidBySid($sid);
        if (!$uid) throw new Exception('cant find user');

        
        $user = Users::model()->findByAttributes(array('id'=>$uid));
        if (!$user) throw new Exception('cant find user');
        
        return $user;
    }
    
    public function actionChangeEmail() {
        
        $email1 = Yii::app()->request->getParam('email1', false);
        $email2 = Yii::app()->request->getParam('email2', false);
        if ($email1 != $email2) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Введенные емейлы не совпадают'
            )));
        }
        
        try {
            $user = $this->_getUser();
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage()
            )));
        }

        $user->email = $email1;
        
        
        $result = array(
            'result' => (int)$user->save()
        );
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionChangePassword() {
        $pass1 = Yii::app()->request->getParam('pass1', false);
        $pass2 = Yii::app()->request->getParam('pass2', false);
        
        if ($pass1 != $pass2) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Введенные пароли не совпадают'
            )));
        }
        
        try {
            $user = $this->_getUser();
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage()
            )));
        }

        $user->password = md5($pass1);
        
        
        $result = array(
            'result' => (int)$user->save()
        );
        $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
