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
                Yii::app()->user->setFlash('error', Yii::t('site', 'Неизветсная ошибка.'));
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
                '<center>
У вас нет вакансий и поэтому вы не сможете создать приглашение. <br/>
                Перейдите на страницу <a href="/profile/corporate/vacancies">вакансии</a> чтоб создать их.
                 </center>'
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
                Yii::app()->createAbsoluteUrl($invite->receiver_id ? '/profile' : '/dashboard/accept-invite/' . $invite->code)
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
        // this page currently will be just RU
        Yii::app()->language = 'ru';

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
        if ($user->id !== $invite->owner_id && $user->id !== $invite->receiver_id) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить чужое приглашение!"
            ));
            $this->redirect('/');
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
    public function actionAcceptInvite($code)
    {
        Yii::app()->language = 'ru';

        $invite = Invite::model()->findByCode($code);
        if (null == $invite) {
            Yii::app()->user->setFlash('error', 'Код неверный');
            $this->redirect('/');
        }

        if((int)$invite->status === Invite::STATUS_EXPIRED){
            Yii::app()->user->setFlash('error', 'У симуляции истек срок давности');
            $this->redirect('/');
        }

        $this->checkUser();

        if (Yii::app()->user->data()->id !== $invite->receiverUser->id) {
            Yii::app()->user->setFlash('error', 'Вы не можете начать чужую симуляци.');
            $this->redirect('/profile');
        }

        $invite->status = Invite::STATUS_ACCEPTED;
        $invite->save(false, ['status']);

        $this->redirect('/simulation/promo/1'); // promo/full
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

        $this->redirect('/dashboard');
    }
}