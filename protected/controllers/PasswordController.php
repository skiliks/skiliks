<?php

/**
 * Контроллер восстановления пароля
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PasswordController extends AjaxController
{

    /**
     * Восстановить пароль - Why "actionRemember" WTF?
     * Br-r-r. So this is remember me or generate new password?
     */
    public function actionRemember()
    {
        $email = Yii::app()->request->getParam('email', false);

        $user = Users::model()->findByAttributes(array('email' => $email));
        if (null === $user) {
            $this->sendJSON(array(
                'result' => 0,
                'message' => 'Не могу найти пользователя с заданным емейл'
            ));
        }

        $password = $user->reinitPassword();

        // отправляем нотификацию
        MailSender::notifyUserAboutPassword($user, $password);

        $result = array(
            'result' => 1
        );
        $this->sendJSON($result);
    }

}

