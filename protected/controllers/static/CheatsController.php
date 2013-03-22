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
        // this page currently will be just RU
        Yii::app()->language = 'ru';

        $user = Yii::app()->user;
        if (null === $user) {
            $this->redirect('/');
        }


        $user = $user->data();

        // protect against real user-cheater
        if (false == $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $this->redirect('/');
        }

        $this->render('mainPage', []);
    }

    /**
     * Cheat
     * @param string $status, Invite::STATUS_XXX
     */
    public function actionSetStatusForAllInvites($status)
    {
        // this page currently will be just RU
        Yii::app()->language = 'ru';

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
            'inviting_user_id' => $user->id
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
        // this page currently will be just RU
        Yii::app()->language = 'ru';

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

        echo "<br><br><a href='/office'>Вернуться на страницу аккаунта.</a><br><br>Done!<br>";

        die;
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

        die;
    }
}
