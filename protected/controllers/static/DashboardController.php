<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

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
            $this->redirect('userAuth/afterRegistrationCorporate');
        }

        $vacancies = [];
        $vacancyList = Vacancy::model()->byUser($this->user->id)->findAll();
        foreach ($vacancyList as $vacancy) {
            $vacancies[$vacancy->id] = Yii::t('site', $vacancy->label);
        }

        $invite = new Invite();
        $validPrevalidate = false;

        if (null !== Yii::app()->request->getParam('prevalidate')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');
            $invite->owner_id = $this->user->id;
            $validPrevalidate = $invite->validate(['firstname', 'lastname', 'email', 'invitations']);
            $profile = YumProfile::model()->findByAttributes(['email' => $invite->email]);

            if ($profile) {
                $invite->receiver_id = $profile->user->id;
            }

            if (null == $invite->vacancy && empty($vacancies)) {
                $invite->clearErrors('vacancy_id');
                $invite->addError('vacancy_id', Yii::t('site', 'Add vacancy in your profile'));
                $validPrevalidate = false;
            }

            if (0 == $this->user->account_corporate->invites_limit) {
                $invite->addError('invitations', 'У вас закончились приглашения');
                $validPrevalidate = false;
            }

            $invite->message = sprintf(
                'Компания %s является лидером российского рынка, ' .
                'известна своим подходом к формированию профессиональной команды ' .
                'и развитию сотрудников на рабочем месте.' .
                "\n" .
                'Вопросы относительно вакансии и прохождения симуляции вы можете ' .
                'задать по адресу %s куратору вакансии %s.',
                $this->user->account_corporate->company_name,
                $this->user->account_corporate->corporate_email,
                $this->user->getFormattedName()
            );

            $invite->signature = sprintf(Yii::t('site', 'Best regards, %s'), $invite->ownerUser->getFormattedName());

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

            $invite->scenario_id = Scenario::model()
                ->findByAttributes(['slug' => Scenario::TYPE_FULL])
                ->getPrimaryKey();

            // send invitation
            if ($invite->validate() && 0 < $this->user->getAccount()->invites_limit) {
                $invite->markAsSendToday();
                $invite->save();
                $this->sendInviteEmail($invite);

                // decline corporate user invites_limit
                $this->user->getAccount()->invites_limit--;
                $this->user->getAccount()->save();
                $this->user->refresh();


                $this->redirect('/dashboard');
            } elseif ($this->user->getAccount()->invites_limit < 1 ) {
                //Yii::app()->user->setFlash('error', Yii::t('site', 'You has no available invites!'));
            } else {
                //Yii::app()->user->setFlash('error', Yii::t('site', 'Неизвестная ошибка.<br/>Приглашение не отправлено.'));
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

                }
            }
        }
        // handle edit invite invitation }

        $simulation = Simulation::model()->getLastSimulation($this->user, Scenario::TYPE_LITE);

        $simulationToDisplayResults = null;
        if (isset(Yii::app()->request->cookies['display_result_for_simulation_id'])) {
            $simulationToDisplayResults = Simulation::model()->findByPk(
                Yii::app()->request->cookies['display_result_for_simulation_id']->value
            );
            unset(Yii::app()->request->cookies['display_result_for_simulation_id']);
        }

        $this->render('dashboard_corporate', [
            'invite'              => $invite,
            'inviteToEdit'        => $inviteToEdit,
            'vacancies'           => $vacancies,
            'validPrevalidate'    => $validPrevalidate,
            'simulation'          => $simulation,
            'display_results_for' => $simulationToDisplayResults,
        ]);
    }

    /**
     *
     */
    public function actionPersonal()
    {
        $this->checkUser();

        $simulation = Simulation::model()->getLastSimulation(Yii::app()->user, Scenario::TYPE_FULL);

        if (null === $simulation) {
            $simulation = Simulation::model()->getLastSimulation(Yii::app()->user, Scenario::TYPE_LITE);
        }

        $simulationToDisplayResults = null;
        if (isset(Yii::app()->request->cookies['display_result_for_simulation_id'])) {
            $simulationToDisplayResults = Simulation::model()->findByPk(
                Yii::app()->request->cookies['display_result_for_simulation_id']->value
            );
            unset(Yii::app()->request->cookies['display_result_for_simulation_id']);
        }

        $this->render('dashboard_personal', [
            'simulation' => $simulation,
            'display_results_for' => $simulationToDisplayResults,
        ]);
    }

    /**
     * @param Invite $invite
     * @return bool
     * @throws CException
     */
    private function sendInviteEmail($invite)
    {
        $this->checkUser();
        if (empty($invite->email)) {
            throw new CException(Yum::t('Email is not set when trying to send invite email. Wrong invite object.'));
        }

        $body = $this->renderPartial('//global_partials/mails/invite', [
            'invite' => $invite
        ], true);

        $mail = [
            'from'        => Yum::module('registration')->registrationEmail,
            'to'          => $invite->email,
            'subject'     => 'Приглашение пройти симуляцию на Skiliks.com',
            'body'        => $body,
            'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mail-top.png',
                    'cid'      => 'mail-top',
                    'name'     => 'mailtop',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-top-2.png',
                    'cid'      => 'mail-top-2',
                    'name'     => 'mailtop2',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-1.png',
                    'cid'      => 'mail-right-1',
                    'name'     => 'mailright1',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-2.png',
                    'cid'      => 'mail-right-2',
                    'name'     => 'mailright2',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-3.png',
                    'cid'      => 'mail-right-3',
                    'name'     => 'mailright3',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
                    'cid'      => 'mail-bottom',
                    'name'     => 'mailbottom',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],
            ],
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
        $this->checkUser();
        $invite = Invite::model()->findByPk($inviteId);
        /* @var $invite Invite */
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

        if ($invite->isPending()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'В ожидании'."
            ));
            $this->redirect('/dashboard');
        }

        if ($invite->isStarted()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'Начато'."
            ));
            $this->redirect('/dashboard');
        }

        $invite->delete();

        $user->getAccount()->increaseLimit($invite);

        $this->redirect('/dashboard');
    }

    /**
     * @param integer $inviteId
     */
    public function actionReSendInvite($inviteId)
    {
        $this->checkUser();
        $invite = Invite::model()->findByPk($inviteId);

        $user = Yii::app()->user;
        if (null === $user) {
            Yii::app()->user->setFlash('success', sprintf(
                "Авторизируйтесь"
            ));
            $this->redirect('/');
        }

        if (null === $invite) {
            //Yii::app()->user->setFlash('success', sprintf(
            //    "Такого приглашения не существует"
            //));
            $this->redirect('/dashboard');
        }

        if (Invite::STATUS_PENDING !== (int)$invite->status) {
            Yii::app()->user->setFlash('success', sprintf(
                nl2br("Только приглашение \n со статусом \"%s\" можно отправить ещё раз."),
                Yii::t('site', Invite::$statusText[Invite::STATUS_PENDING])
            ));
            $this->redirect('/dashboard');
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

        $this->sendInviteEmail($invite);

        $this->redirect('/dashboard');
    }

    /**
     * @param $code
     */
    public function actionAcceptInvite($id)
    {
        $this->checkUser();
        $invite = Invite::model()->findByPk($id);
        if (null == $invite) {
            $this->redirect('/dashboard');
        }

        if((int)$invite->status === Invite::STATUS_EXPIRED){
            Yii::app()->user->setFlash('error', 'У симуляции истек срок давности');
            $this->redirect('/');
        }

        if((int)$invite->status !== Invite::STATUS_PENDING){

            $this->redirect('/dashboard');
        }

        $this->checkUser();

        if (Yii::app()->user->data()->profile->email !== $invite->email) {
            Yii::app()->user->setFlash('error', 'Вы не можете начать чужую симуляцию.');
            $this->redirect('/profile');
        }

        // for invites to unregistered (when invitation had been send) users, receiver_id is NULL
        // fix (NULL) receiver_id to make sure that simulation can start
        $invite->receiver_id = Yii::app()->user->data()->id;
        $invite->status = Invite::STATUS_ACCEPTED;
        $invite->update(false, ['status', 'receiver_id']);

        /* @flash
        Yii::app()->user->setFlash('success', sprintf(
            'Приглашение от %s %s принято.',
            $invite->getCompanyOwnershipType(),
            ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
        ));
         */

        $this->redirect('/dashboard'); // promo/full
    }

    /**
     *
     * @param string $code
     */
    public function actionSoftRemoveInvite($id)
    {
        $this->checkUser();

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

        /* @flash
        Yii::app()->user->setFlash('success', sprintf(
            'Приглашение от %s %s отклонено.',
            $invite->getCompanyOwnershipType(),
            ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
        ));
         */

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
            Yii::app()->user->data()->profile->email !== $declineExplanation->invite->email &&
            null !== $declineExplanation->invite->receiver_id) {

            Yii::app()->user->setFlash('success', 'Вы не можете удалить чужое приглашение.');
            $this->redirect('/dashboard');
        }

        $declineExplanation->invite->ownerUser->getAccount()->invites_limit++;
        $declineExplanation->invite->ownerUser->getAccount()->save(false);

        $declineExplanation->invite_recipient_id = $declineExplanation->invite->receiver_id;
        $declineExplanation->invite_owner_id = $declineExplanation->invite->owner_id;
        $declineExplanation->vacancy_label = $declineExplanation->invite->getVacancyLabel();
        $declineExplanation->created_at = date('Y-m-d H:i:s');
        $declineExplanation->save();

        $declineExplanation->invite->status = Invite::STATUS_DECLINED;
        $declineExplanation->invite->update(false, ['status']);

        // for unregistered user - redirect to homepage
        if (null === Yii::app()->user->data()->id) {
            $this->redirect('/');
        }

        /* @var $user YumUser */
        $user = Yii::app()->user->data();

        if($user->isAuth()) {
            Yii::app()->user->setFlash('success', sprintf(
                'Вы всегда можете <a href="/registration">зарегистрироваться</a> снова на главной странице и начать использовать наш продукт.
                Мы верим, что он обязательно Вам понравится и окажется полезным.'
            ));
            $this->redirect('/');

        } elseif($user->isPersonal()) {
            /*
            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s отклонено.',
                $declineExplanation->invite->getCompanyOwnershipType(),
                ($declineExplanation->invite->getCompanyName() === null)?"компании":$declineExplanation->invite->getCompanyName()
            ));
            */
            $this->redirect('/dashboard');
        }
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
                    Yii::app()->user->isGuest ? '': 'registration_only!=1',
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