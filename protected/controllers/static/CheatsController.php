<?php

/**
 * Class CheatsController
 *
 * Теперь он нужен только для селениум тестов
 */
class CheatsController extends SiteBaseController
{
    /**
     * Логинит пользователя под ником seleium@skiliks.com (тестовый пользователь)
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
            //$end = false
            $user = YumUser::model()->findByAttributes([
                'username' => 'selenium'
            ]);
        }

        if (isset($cookies['cook_dev_ejbfksbfeksfesfbefbjbbooisnsddsjkfsfnesgjsf'])
            && $cookies['cook_dev_ejbfksbfeksfesfbefbjbbooisnsddsjkfsfnesgjsf']->value == 'adeshflewfvgiu3428dfgfgdgfg32fgdfgghfgh34e324rfqvf4g534hg54gh5')  {
            //$end = false;
            $user = YumUser::model()->findByAttributes([
                'username' => 'selenium1'
            ]);
        }

//        )
//            && false === isset($cookies['cook_dev_ejbfksbfeksfesfbefbjbbooisnsddsjkfsfnesgjsf']
//        if () {
//            $end = true;
//        }
//
//        if ($end === true)
//        {
//            Yii::app()->end();
//        }
//
//        if (true === isset($cookies['cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds']))
//        {
//            $user = YumUser::model()->findByAttributes([
//                'username' => 'selenium'
//            ]);
//        }
//        if (true === isset($cookies['cook_dev_ejbfksbfeksfesfbefbjbbooisnsddsjkfsfnesgjsf']))
//        {
//            $user = YumUser::model()->findByAttributes([
//                'username' => 'selenium1'
//            ]);
//        }

        if (null === $user) {
            throw new Exception('User not found.');
        }

        $login = new YumUserIdentity($user->username, false);
        $login->authenticate(true);
        Yii::app()->user->login($login);

        $this->redirect('/simulation/developer/full');
    }
}
