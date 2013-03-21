<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/21/13
 * Time: 11:56 AM
 * To change this template use File | Settings | File Templates.
 */

class InvitesController extends YumController
{
    /**
     * @param integer $inviteId
     */
    public function actionRemove($inviteId)
    {
        // this page currently will be just RU
        Yii::app()->language = 'ru';

        $invite = Invite::model()->findByPk($inviteId);

        $user = Yii::app()->user;
        if (null === $user) {
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // you can`t delete other (corporate) user invite
        if ($user->id !== $invite->inviting_user_id) {
            $this->redirect('/');
        }

        if (false === $user->isCorporate()) {
            $this->redirect('/');
        }

        if (false == $invite->isPending()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить подтвердённое приглашение!"
            ));
            $this->redirect('/dashboard');
        }

        $firstname = $invite->firstname;
        $lastname  = $invite->lastname;

        $invite->delete();

        $user->getAccount()->invites_limit++;
        $user->getAccount()->save();
        $user->getAccount()->refresh();

        Yii::app()->user->setFlash('success', sprintf(
            "Приглашение для %s %s удалено!",
            $firstname,
            $lastname
        ));

        $this->redirect('/dashboard');
    }

    /**
     * @param integer $inviteId
     */
    public function actionReSend($inviteId)
    {
        // this page currently will be just RU
        Yii::app()->language = 'ru';

        $invite = Invite::model()->findByPk($inviteId);

        $user = Yii::app()->user;
        if (null === $user) {
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // you can`t delete other (corporate) user invite
        if ($user->id !== $invite->inviting_user_id) {
            $this->redirect('/');
        }

        if (false === $user->isCorporate()) {
            $this->redirect('/');
        }

        $invite->markAsSendToday();
        $invite->update(['sent_time']);

        Yii::app()->user->setFlash('success', sprintf(
            "Приглашение для %s %s отсрочено до %s!",
            $invite->firstname,
            $invite->lastname,
            $invite->getExpiredDate()
        ));

        $this->redirect('/dashboard');
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
}