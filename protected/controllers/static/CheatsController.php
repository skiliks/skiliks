<?php

class CheatsController extends SiteBaseController
{
    /**
     * User private page -> "user cabinet"
     */
    public function actionMainPage()
    {
        $user = Yii::app()->user;
        if (null === $user) {
            $this->redirect('/');
        }


        $user = $user->data();

        // protect against real user-cheater
        if (false == $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $this->redirect('/');
        }

        $this->render('mainPage', [
            'scenarios' => Scenario::model()->findAll(),
        ]);
    }

    /**
     * Cheat
     */
    public function actionIncreaseInvites()
    {
        $user = Yii::app()->user;
        if (null === $user) {
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // protect against real user-cheater
        if (false == $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $this->redirect('/');
        }

        if (false == $user->isCorporate()) {
            $this->redirect('/');
        }

        $initValue = $user->getAccount()->invites_limit;

        $user->getAccount()->invites_limit += 10;
        $user->getAccount()->save();

        UserService::logCorporateInviteMovementAdd(
            'Cheats: actionIncreaseInvites',
            $user->getAccount(),
            $initValue
        );

        Yii::app()->user->setFlash('success', "Вам добавлено 10 приглашений!");

        $this->redirect('/dashboard');
    }

    /**
     * Cheat
     */
    public function actionChooseTariff($label = null)
    {
        if (null == $label) {
            $label = Yii::app()->request->getParam('label');
        }

        $user = Yii::app()->user;
        if (null === $user) {
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // protect against real user-cheater
        if (false == $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $this->redirect('/');
        }

        if (false == $user->isCorporate()) {
            $this->redirect('/');
        }

        $tariff = Tariff::model()->findByAttributes(['slug' => $label]);

        if (null == $tariff) {

            $initValue = 0;

            $user->getAccount()->tariff_id = null;
            $user->getAccount()->tariff_activated_at = null;
            $user->getAccount()->tariff_expired_at = null;
            $user->getAccount()->invites_limit = 0;
            $user->getAccount()->save();

            UserService::logCorporateInviteMovementAdd(
                'Cheats: actionChooseTariff, NULL tariff',
                $user->getAccount(),
                $initValue
            );

            $this->redirect('/profile/corporate/tariff');
        }

        $initValue = $user->getAccount()->invites_limit;

        $user->getAccount()->tariff_id = $tariff->id;
        $user->getAccount()->tariff_activated_at = date('Y-m-d H:i:s');
        $user->getAccount()->tariff_expired_at = date('Y-').(date('m')+1).date('-d H:i:s');
        $user->getAccount()->invites_limit = $tariff->simulations_amount;
        $user->getAccount()->save();

        UserService::logCorporateInviteMovementAdd(
            'Cheats: actionChooseTariff, tariff not null',
            $user->getAccount(),
            $initValue
        );

        Yii::app()->user->setFlash('success', sprintf('Вам активирован тарифный план "%s"!', $label));

        $this->redirect('/profile/corporate/tariff');
    }

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
