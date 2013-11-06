<?php

/**
 * Class CheatsController
 *
 * Теперь он нужен только для селениум тестов
 */
class CheatsController extends SiteBaseController
{
    /**
     * Логинит пользователя под ником seleium.engine@skiliks.com (тестовый пользователь)
     * И перенаправляет к началу полной дев симуляции
     *
     * Для защиты от читтинга проверяем cookie со странным длинным именем и странным длинным названием
     * cookie(cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds = 'dsiucskcmnxkcjzhxciaowi2039ru948fysuhfiefds8v7sd8djkedbjsaicu9')
     */
    public function actionStartSimulationForFastSeleniumTest()
    {

        $cookies = Yii::app()->request->cookies;
        //var_dump($cookies); die;
        $user = null;
        if (isset($cookies['cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds'])
            && $cookies['cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds']->value == 'dsiucskcmnxkcjzhxciaowi2039ru948fysuhfiefds8v7sd8djkedbjsaicu9')  {
            $user = YumUser::model()->findByAttributes([
                'username' => 'seleniumEngine'
            ]);
        }

        if (isset($cookies['cook_dev_ejbfksbfeksfesfbefbjbbooisnsddsjkfsfnesgjsf'])
            && $cookies['cook_dev_ejbfksbfeksfesfbefbjbbooisnsddsjkfsfnesgjsf']->value == 'adeshflewfvgiu3428dfgfgdgfg32fgdfgghfgh34e324rfqvf4g534hg54gh5')  {
            $user = YumUser::model()->findByAttributes([
                'username' => 'seleniumAssessment'
            ]);
        }

        if (null === $user) {
            throw new Exception('User not found.');
        }

        $login = new YumUserIdentity($user->username, false);
        $login->authenticate(true);
        Yii::app()->user->login($login);

        $this->redirect('/simulation/developer/full');
    }
}
