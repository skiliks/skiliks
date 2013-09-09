<?php

/**
 * Class CheatsController
 *
 * Теперь он нужен только для селениум тестов
 */
class CheatsController extends SiteBaseController
{
    /**
     * Логинит пользователя под ником asd@skiliks.com (тестовый пользователь)
     * И перенаправляет к началу полной дев симуляции
     *
     * Для защиты от читтинга проверяем cookie со странным длинным именем и странным длинным названием
     * cookie(cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds = 'dsiucskcmnxkcjzhxciaowi2039ru948fysuhfiefds8v7sd8djkedbjsaicu9')
     */
    public function actionStartSimulationForFastSeleniumTest()
    {
        $cookies = Yii::app()->request->cookies;

        if (false === isset($cookies['cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds'])) {
            Yii::app()->end();
        }

        if ($cookies['cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds']->value !== 'dsiucskcmnxkcjzhxciaowi2039ru948fysuhfiefds8v7sd8djkedbjsaicu9') {
            Yii::app()->end();
        }

        $user = YumUser::model()->findByAttributes([
            'username' => 'selenium'
        ]);

        if (null === $user) {
            throw new Exception('User not found.');
        }

        $login = new YumUserIdentity($user->username, false);
        $login->authenticate(true);
        Yii::app()->user->login($login);

        $this->redirect('/simulation/developer/full');
    }
}
