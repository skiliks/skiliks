<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/21/13
 * Time: 10:46 PM
 * To change this template use File | Settings | File Templates.
 */

class DashboardController extends AjaxController implements AccountPageControllerInterface
{
    public function getBaseViewPath()
    {
        return '/static/dashboard';
    }

    public function actionIndex()
    {
        $this->accountPagesBase();
    }

    public function actionCorporate()
    {
        Yii::app()->language = 'ru';

        $this->checkUser();

        if (false === $this->user->isCorporate() ||
            empty($this->user->account_corporate->is_corporate_email_verified)
        ) {
            $this->redirect('/');
        }

        $invite = new Invite();
        $valid = false;

        if (null !== Yii::app()->request->getParam('prevalidate')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');
            $invite->inviting_user_id = $this->user->id;
            $valid = $invite->validate(['firstname', 'lastname', 'email']);
            $profile = YumProfile::model()->findByAttributes(['email' => $invite->email]);
            $position_label = Yii::t('site', (string)$invite->position->label);
            if ($profile) {
                $invite->message = "Зайдите пожалуйста в ваш кабинет, работодатель отправил вам приглашение пройти оценивание уровня менеджерских навыков на позицию $position_label.";
            } else {
                $invite->message = "Работодатель заинтересован в вашей кандидатуре на позицию $position_label Для кандидата на данную позицию обязательным условием \n"
                    ."является прохождение оценивания для определениям уровня менеджерских навыков. Для этого вам необходимо перейти по ссылке, \n"
                    ."зарегистрироваться и запустить ассессмент.";
            }

            $invite->signature = Yii::t('site', 'Best regards');
        }

        // handle send invitation {
        if (null !== Yii::app()->request->getParam('send')) {

            $invite->attributes = Yii::app()->request->getParam('Invite');

            $invite->code = uniqid(md5(mt_rand()));
            $invite->inviting_user_id = $this->user->id;

            // What happens if user is registered, but not activated??
            $profile = YumProfile::model()->findByAttributes([
                'email' => $invite->email
            ]);
            if ($profile) {
                $invite->invited_user_id = $profile->user->id;
            }

            // send invitation
            if ($invite->validate() && 0 < $this->user->getAccount()->invites_limit) {
                $invite->markAsSendToday();
                $invite->save();
                $this->sendInviteEmail($invite);

                // decline corporate user invites_limit
                $this->user->getAccount()->invites_limit--;
                $this->user->getAccount()->save();
                $this->user->refresh();

                Yii::app()->user->setFlash('success', 'Приглашение успешно выслано');

                $this->redirect('/dashboard');
            } elseif ($this->user->getAccount()->invites_limit < 0 ) {
                Yii::app()->user->setFlash('error', Yii::t('site', 'You has no available invites!'));
            }
        }
        // handle send invitation }

        // handle edit invite invitation {
        $inviteToEdit = new Invite();
        if (null !== Yii::app()->request->getParam('edit-invite')) {
            $inviteData = Yii::app()->request->getParam('Invite');

            $inviteToEdit = Invite::model()->findByPk($inviteData['id']);

            if (null === $invite) {
                Yii::app()->user->setFlash('error', sprintf(
                    "Неправильные данные!"
                ));
            } else {
                $inviteToEdit->firstname = $inviteData['firstname'];
                $inviteToEdit->lastname = $inviteData['lastname'];
                $inviteToEdit->position_id = $inviteData['position_id'];
                // send invitation
                if ($inviteToEdit->validate(['firstname', 'lastname', 'position_id'])) {
                    $inviteToEdit->update(['firstname', 'lastname', 'position_id']);
                    $inviteToEdit->refresh();

                    Yii::app()->user->setFlash('success', sprintf(
                        'Приглашение для %s %s успешно сохранено.',
                        $inviteToEdit->firstname,
                        $inviteToEdit->lastname
                    ));
                }
            }
        }
        // handle edit invite invitation }

        $positions = [];
        foreach (Position::model()->findAll() as $position) {
            $positions[$position->id] = Yii::t('site', $position->label);
        }

        $this->render('dashboard_corporate', [
            //'user' => $this->user,
            'invite'       => $invite,
            'inviteToEdit' => $inviteToEdit,
            'positions'    => $positions,
            'valid'        => $valid
        ]);
    }

    /**
     *
     */
    public function actionPersonal()
    {
        $this->render('dashboard_personal', []);
    }

    /**
     * @param Invite $invite
     * @return bool
     * @throws CException
     */
    private function sendInviteEmail($invite)
    {
        if (empty($invite->email)) {
            throw new CException(Yum::t('Email is not set when trying to send invite email. Wrong invite object.'));
        }

        $body = [
            'Уважаемый ' . $invite->getFullname(),
            $invite->message,
            'Пройдите по ссылке чтобы начать симуляцию',
            sprintf(
                '<a href="%1$s" target="_blank">%1$s</a>',
                Yii::app()->createAbsoluteUrl($invite->invited_user_id ? '/profile' : '/dashboard/accept-invite/' . $invite->code)
            ),
            $invite->signature
        ];

        $mail = [
            'from'    => Yum::module('registration')->registrationEmail,
            'to'      => $invite->email,
            'subject' => 'Приглашение пройти симуляцию на Skiliks.com',
            'body'    => implode("<br />", $body)
        ];

        $invite->markAsSendToday();
        $invite->save();

        $sent = YumMailer::send($mail);

        return $sent;
    }

    /**
     * @param integer $inviteId
     */
    public function actionRemoveInvite($inviteId)
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
    public function actionReSendInvite($inviteId)
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
     * @param $code
     */
    public function actionAcceptInvite($code)
    {
        Yii::app()->language = 'ru';

        $invite = Invite::model()->findByCode($code);
        if (empty($invite)) {
            Yii::app()->user->setFlash('site', 'Код неверный');
            $this->redirect('/');
        }

        $this->user = new YumUser('registration');
        $profile = new YumProfile('registration');
        $account = new UserAccountPersonal();
        $error = null;

        $YumUser    = Yii::app()->request->getParam('YumUser');
        $YumProfile = Yii::app()->request->getParam('YumProfile');
        $UserAccount = Yii::app()->request->getParam('UserAccountPersonal');

        if(null !== $YumUser && null !== $YumProfile && null !== $UserAccount)
        {
            $this->user->attributes = $YumUser;
            $profile->attributes = $YumProfile;
            $account->attributes = $UserAccount;

            $profile->email = $invite->email;
            $this->user->setUserNameFromEmail($profile->email);

            // Protect from "Wrong username" message - we need "Wrong email", from Profile form
            if (null == $this->user->username) {
                $this->user->username = 'DefaultName';
            }

            $userValid = $this->user->validate();
            $profileValid = $profile->validate();

            if ($userValid && $profileValid) {
                $result = $this->user->register($this->user->username, $this->user->password, $profile);

                if (false !== $result) {
                    $account->user_id = $this->user->id;
                    $account->save();

                    $invite->status = Invite::STATUS_ACCEPTED;
                    $invite->save();

                    $activation_result = YumUser::activate($profile->email, $this->user->activationKey);
                    $this->user->authenticate($YumUser['password']);
                    // TODO: Change redirection point
                    $this->redirect('/registration/account-type/added');
                } else {
                    $this->user->password = '';
                    $this->user->password_again = '';

                    echo 'Can`t register.';
                }
            }
        }

        $industries = [];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = Yii::t('site', $industry->label);
        }

        $positions = [];
        foreach (Position::model()->findAll() as $position) {
            $positions[$position->id] = Yii::t('site', $position->label);
        }

        $this->render(
            'registrationByLink',
            [
                'invite'     => $invite,
                'user'       => $this->user,
                'profile'    => $profile,
                'account'    => $account,
                'industries' => $industries,
                'positions'  => $positions,
                'error'      => $error
            ]
        );
    }

    /**
     * @param $code
     */
    public function actionDeclineInvite($code)
    {
        $invite = Invite::model()->findByCode($code);
        if (empty($invite)) {
            $this->redirect('/');
        }

        $reason = Yii::app()->request->getParam('reason');
        $reasonDesc = Yii::app()->request->getParam('reason-desc');

        $invite->status = Invite::STATUS_DECLINED;
        $invite->save();

        // TODO: Change redirection point
        $this->redirect('/');
    }
}