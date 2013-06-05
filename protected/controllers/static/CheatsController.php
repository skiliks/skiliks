<?php

class CheatsController extends AjaxController
{
    public $user;
    public $signInErrors = [];



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
     * @param string $status, Invite::STATUS_XXX
     */
    public function actionSetStatusForAllInvites($status)
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

        $invitations = Invite::model()->findAllByAttributes([
            'owner_id' => $user->id
        ]);

        foreach ($invitations as $invitation) {
            $invitation->status = (int)Invite::$statusId[$status];
            $invitation->update(['status']);
        }

        Yii::app()->user->setFlash('success', sprintf(
            "Все приглашения теперь в статусе %s!",
            Yii::t('site', $status)
        ));

        $this->redirect('/dashboard');
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

        $user->getAccount()->invites_limit += 10;
        $user->getAccount()->save();

        Yii::app()->user->setFlash('success', "Вам добавлено 10 приглашений!");

        $this->redirect('/dashboard');
    }


    /**
     * Action for testing - allow reset authorized user account type
     */
    public function actionCleanUpAccount()
    {
        $this->checkUser();

        if (null !== $this->user->account_personal) {
            $this->user->account_personal->delete();
            echo "<br>Personal accoutn removed.<br>";
        }

        if (null !== $this->user->account_corporate) {
            $this->user->account_corporate->delete();
            echo "<br>Corporate accoutn removed.<br>";
        }

        echo "<br><br><a href='/cheats'>Вернуться на страницу аккаунта.</a><br><br>Done!<br>";

        Yii::app()->end(); // кошерное die
    }

    /**
     *
     */
    public function actionListOfSubscriptions() {

        $emails = Yii::app()->db->createCommand()
            ->select( 'id, email' )
            ->from( 'emails_sub' )
            ->queryAll();
        echo 'ID EMAIL <br>';
        foreach ($emails as $email) {

            echo "{$email['id']} {$email['email']} <br>";
        }

        Yii::app()->end(); // кошерное die
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

            $user->getAccount()->tariff_id = null;
            $user->getAccount()->tariff_activated_at = null;
            $user->getAccount()->tariff_expired_at = null;
            $user->getAccount()->invites_limit = 0;
            $user->getAccount()->save();

            $this->redirect('/profile/corporate/tariff');
        }

        $user->getAccount()->tariff_id = $tariff->id;
        $user->getAccount()->tariff_activated_at = date('Y-m-d H:i:s');
        $user->getAccount()->tariff_expired_at = date('Y-').(date('m')+1).date('-d H:i:s');
        $user->getAccount()->invites_limit = $tariff->simulations_amount;
        $user->getAccount()->save();

        Yii::app()->user->setFlash('success', sprintf('Вам активирован тарифный план "%s"!', $label));

        $this->redirect('/profile/corporate/tariff');
    }

    /**
     *
     */
    public function actionAssessmentsGrid()
    {
        $data = [
            'Итоговый рейтинг менеджера' => [
                'Управленческие характеристики' => [],
                'Результативность' => [],
                'Эффективность использования времени' => [],
            ],
            'Личные характеристики' => [[]],
        ];

        $fullScenario  = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $learningAreas = $fullScenario->getLearningAreas();

        foreach ($learningAreas as $learningArea) {
            $data['Итоговый рейтинг менеджера']['Управленческие характеристики'][$learningArea->title] = '';
            $data['Итоговый рейтинг менеджера']['Результативность'][] = '';
            $data['Итоговый рейтинг менеджера']['Эффективность использования времени'][] = '';
            $data['Личные характеристики'][0][] = '';
        }

        $this->render('assessment_grid', [
            'data' => $data
        ]);
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
            'username' => 'asd'
        ]);

        if (null === $user) {
            throw new Exception('User not found.');
        }

        $login = new YumUserIdentity($user->username, false);
        $login->authenticate(true);
        Yii::app()->user->login($login);

        $this->redirect('/simulation/developer/full');
    }

    /**
     *
     */
    public function actionSaveZohoUsageStatus($value, $expireDate)
    {
        //  $value = Yii::app()->request->getParam('value');
        $usages_today = $value;

        if (null !== $usages_today) {
            $file = fopen(__DIR__ . '/../../../tmp/zohoUsageStatistic.dat', 'c');
            $data = $usages_today . 'UDS, '. $expireDate;
            $data = str_replace([ '___','__','_'],[' - ',', ',', '],$data);
            fwrite($file, $data);
            fclose($file);
        }

        Yii::app()->end();
    }

    /**
     *
     */
    public function actionGetZohoUsageStatus()
    {
        @$file = fopen(__DIR__ . '/../../../tmp/zohoUsageStatistic.dat', 'r');
        if (null !== $file) {
            $data = fread($file, 100);
            fclose($file);

            echo urldecode($data);
        } else {
            echo 'No statistic.';
        }

        Yii::app()->end();
    }
}
