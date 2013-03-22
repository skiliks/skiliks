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
}