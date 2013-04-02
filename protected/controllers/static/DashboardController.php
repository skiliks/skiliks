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
        $this->checkUser();

        if (false === $this->user->isCorporate() ||
            empty($this->user->account_corporate->is_corporate_email_verified)
        ) {
            $this->redirect('/');
        }

        $invite = new Invite();
        $validPrevalidate = false;

        if (null !== Yii::app()->request->getParam('prevalidate')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');
            $invite->owner_id = $this->user->id;
            $validPrevalidate = $invite->validate(['firstname', 'lastname', 'email']);
            $profile = YumProfile::model()->findByAttributes(['email' => $invite->email]);
            $vacancy_label = Yii::t('site', (string)$invite->vacancy->label);
            if ($profile) {
                $invite->message = "Зайдите пожалуйста в ваш кабинет, работодатель отправил вам приглашение пройти оценивание уровня менеджерских навыков на позицию $vacancy_label.";
            } else {
                $invite->message = "Работодатель заинтересован в вашей кандидатуре на позицию $vacancy_label Для кандидата на данную позицию обязательным условием \n"
                    ."является прохождение оценивания для определениям уровня менеджерских навыков. Для этого вам необходимо перейти по ссылке, \n"
                    ."зарегистрироваться и запустить ассессмент.";
            }

            $invite->signature = Yii::t('site', 'Best regards');
        }

        // handle send invitation {
        if (null !== Yii::app()->request->getParam('send')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');

            $invite->code = uniqid(md5(mt_rand()));
            $invite->owner_id = $this->user->id;

            // What happens if user is registered, but not activated??
            $profile = YumProfile::model()->findByAttributes([
                'email' => $invite->email
            ]);
            if ($profile) {
                $invite->receiver_id = $profile->user->id;
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
            } elseif ($this->user->getAccount()->invites_limit < 1 ) {
                Yii::app()->user->setFlash('error', Yii::t('site', 'You has no available invites!'));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('site', 'Неизвестная ошибка.'));
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
                $inviteToEdit->vacancy_id = $inviteData['vacancy_id'];
                // send invitation
                if ($inviteToEdit->validate(['firstname', 'lastname', 'vacancy_id'])) {
                    $inviteToEdit->update(['firstname', 'lastname', 'vacancy_id']);
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

        $vacancies = [];
        $vacancyList = Vacancy::model()->byUser($this->user->id)->findAll();
        foreach ($vacancyList as $vacancy) {
            $vacancies[$vacancy->id] = Yii::t('site', $vacancy->label);
        }

        if (0 == count($vacancies)) {
            Yii::app()->user->setFlash('error', sprintf(
                'У вас нет вакансий и поэтому вы не сможете создать приглашение. <br/>
                Перейдите на страницу <a href="/profile/corporate/vacancies">вакансии</a> чтоб создать их.'
            ));
        }

        $this->render('dashboard_corporate', [
            //'user' => $this->user,
            'invite'             => $invite,
            'inviteToEdit'       => $inviteToEdit,
            'vacancies'          => $vacancies,
            'validPrevalidate'   => $validPrevalidate,
        ]);
    }

    /**
     *
     */
    public function actionPersonal()
    {
        $simulation = Simulation::model()->getLastSimulation(Yii::app()->user);

        $this->render('dashboard_personal', ['simulation'=>$simulation]);
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
            'Пройдите по ссылке чтобы одобрить приглашение пройти симуляцию',
            sprintf(
                '<a href="%1$s" target="_blank">%1$s</a>',
                Yii::app()->createAbsoluteUrl($invite->receiver_id ? '/dashboard' : '/registration/by-link/' . $invite->code)
            ),
            'Приглашние утратит силу через неделю.',
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

        try {
        $sent = YumMailer::send($mail);
        } catch (phpmailerException $e) {
            // happens at my local PC only, Slavka
            $sent = null;
        }

        return $sent;
    }

    /**
     * @param integer $inviteId
     */
    public function actionRemoveInvite($inviteId)
    {
        $invite = Invite::model()->findByPk($inviteId);

        $user = Yii::app()->user;
        if (null === $user) {
            Yii::app()->user->setFlash('success', sprintf(
                "Авторизируйтесь"
            ));
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // owner only can delete his invite
        if ($user->id !== $invite->owner_id) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить чужое приглашение!"
            ));
            $this->redirect('/');
        }

        $firstname = $invite->firstname;
        $lastname  = $invite->lastname;

        $invite->delete();

        $user->getAccount()->increaseLimit($invite);

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
        $invite = Invite::model()->findByPk($inviteId);

        $user = Yii::app()->user;
        if (null === $user) {
            Yii::app()->user->setFlash('success', sprintf(
                "Авторизируйтесь"
            ));
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // you can`t delete other (corporate) user invite
        if ($user->id !== $invite->owner_id) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя продлить чужое приглашение!"
            ));
            $this->redirect('/');
        }

        if (false === $user->isCorporate()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Только корпоративный пользователь пожет продлить приглашение!"
            ));
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
    public function actionAcceptInvite($id)
    {
        $invite = Invite::model()->findByPk($id);
        if (null == $invite) {
            Yii::app()->user->setFlash('error', 'Приглашения с таким ID не существует.');
            $this->redirect('/');
        }

        if((int)$invite->status === Invite::STATUS_EXPIRED){
            Yii::app()->user->setFlash('error', 'У симуляции истек срок давности');
            $this->redirect('/');
        }

        if((int)$invite->status !== Invite::STATUS_PENDING){
            Yii::app()->user->setFlash(
                'error',
                sprintf(
                    'Это приглашение уже обработано,<br/> его статус "%s".',
                    Yii::t('site', Invite::$statusText[$invite->status])
                )
            );
            $this->redirect('/');
        }

        $this->checkUser();

        if (Yii::app()->user->data()->id !== $invite->receiverUser->id) {
            Yii::app()->user->setFlash('error', 'Вы не можете начать чужую симуляцию.');
            $this->redirect('/profile');
        }

        $invite->status = Invite::STATUS_ACCEPTED;
        $invite->save(false, ['status']);

        Yii::app()->user->setFlash('success', sprintf(
            'Приглашение от %s %s принято.',
            $invite->ownerUser->getAccount()->ownership_type,
            $invite->ownerUser->getAccount()->company_name
        ));

        $this->redirect('/dashboard'); // promo/full
    }

    /**
     *
     * @param string $code
     */
    public function actionSoftRemoveInvite($id)
    {
        $invite = Invite::model()->findByPk($id);

        if (null === $invite) {
            Yii::app()->user->setFlash('success', 'Выбранного к отмене приглашения не существует.');
            $this->redirect('/dashboard');
        }

        if (Yii::app()->user->data()->id !== $invite->receiver_id &&
            Yii::app()->user->data()->id !== $invite->owner_id) {

            Yii::app()->user->setFlash('success', 'Вы не можете удалить чужое приглашение.');
            $this->redirect('/dashboard');
        }

        $invite->status = Invite::STATUS_DECLINED;
        $invite->update(false, ['status']);

        Yii::app()->user->setFlash('success', sprintf(
            'Приглашение от %s %s отклонено.',
            $invite->ownerUser->getAccount()->ownership_type,
            $invite->ownerUser->getAccount()->company_name
        ));

        $this->redirect('/dashboard');
    }

    /**
     *
     * @param string $code
     */
    public function actionDeclineInvite($id)
    {
        $declineExplanation = new DeclineExplanation();
        $declineExplanation->attributes = Yii::app()->request->getParam('DeclineExplanation');

        if (null === $declineExplanation->invite) {
            Yii::app()->user->setFlash('success', 'Выбранного к отмене приглашения не существует.');
            $this->redirect('/dashboard');
        }

        if (Yii::app()->user->data()->id !== $declineExplanation->invite->receiver_id &&
            Yii::app()->user->data()->id !== $declineExplanation->invite->owner_id &&
            null !== $declineExplanation->invite->receiver_id) {

            Yii::app()->user->setFlash('success', 'Вы не можете удалить чужое приглашение.');
            $this->redirect('/dashboard');
        }

        $declineExplanation->invite->ownerUser->getAccount()->invites_limit++;
        $declineExplanation->invite->ownerUser->getAccount()->save(false);

        $declineExplanation->invite_recipient_id = $declineExplanation->invite->receiver_id;
        $declineExplanation->invite_owner_id = $declineExplanation->invite->owner_id;
        $declineExplanation->vacancy_label = $declineExplanation->invite->vacancy->label;
        $declineExplanation->created_at = date('Y-m-d H:i:s');
        $declineExplanation->save();

        $declineExplanation->invite->status = Invite::STATUS_DECLINED;
        $declineExplanation->invite->update(false, ['status']);

        Yii::app()->user->setFlash('success', sprintf(
            'Приглашение от %s %s отклонено.',
            $declineExplanation->invite->ownerUser->getAccount()->ownership_type,
            $declineExplanation->invite->ownerUser->getAccount()->company_name
        ));

        // for unregistered user - redirect to homepage
        if (null === Yii::app()->user->data()->id) {
            $this->redirect('/');
        }

        $this->redirect('/dashboard');
    }

    /**
     *
     */
    public function actionValidateDeclineExplanation()
    {
        $declineExplanation = new DeclineExplanation();

        $declineExplanation->attributes = Yii::app()->request->getParam('DeclineExplanation');
        $isValid = false;

        $reasonOther = DeclineReason::model()->findByAttributes(['alias' => 'other']);

        // no object - no validation -> this is request to render form at first
        if (null !== Yii::app()->request->getParam('DeclineExplanation')) {
            // fill 'description' from 'reason->label' {
            if (null !== $reasonOther) {
                if ($declineExplanation->reason_id != $reasonOther->id) {
                    $declineExplanation->description = $reasonOther->label;
                }
            }
            // fill 'description' from 'reason->label' }

            $isValid = $declineExplanation->validate(['reason_id', 'description']);
        }

        $this->layout = false;

        $html = $this->render(
            'decline_explanation_form',
            [
                'declineExplanation' => $declineExplanation,
                'reasons'            => StaticSiteTools::formatValuesArrayLite(
                    'DeclineReason',
                    'id',
                    'label',
                    '',
                    false,
                    ' ORDER BY sort_order DESC'
                ),
                'action' => '/dashboard/decline-invite/'.(int)$declineExplanation->invite_id,
                'reasonOtherId' => (null === $reasonOther) ? '' : $reasonOther->id,
            ],
            true
        );

        $this->sendJSON([
            'isValid' => $isValid,
            'html'    => $html,
         ]);
    }
}