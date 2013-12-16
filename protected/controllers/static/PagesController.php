<?php

class PagesController extends SiteBaseController
{
    public $is_test = false;

    public function beforeAction($action)
    {
        $this->user = Yii::app()->user->data();
        if (!$this->user->isAuth() && $this->user->account_corporate && !$this->user->isActive()) {
            $this->redirect('/userAuth/afterRegistration');
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        /* @var $user YumUser */
        $this->render('home', [
            'assetsUrl'          => $this->getAssetsUrl(),
            'userSubscribed'     => false,
            'httpUserAgent'      => $_SERVER['HTTP_USER_AGENT'],
            'isSkipBrowserCheck' => (int)Yii::app()->params['public']['isSkipBrowserCheck'],
        ]);
    }

    /**
     *
     */
    public function actionTeam()
    {
        $this->render('team');
    }

    /**
     *
     */
    public function actionProduct()
    {
        $this->render('product');
    }

    /**
     *
     */
    public function actionTariffs()
    {
        if($this->user->isAuth()) {
            Yii::app()->setLanguage("ru");
        }

        $this->render('tariffs', [
            'tariffs' => Tariff::model()->findAll('',['order' => 'order ASD']), 'user' => $this->user
        ]);
    }

    /**
     *
     */
    public function actionSystemMismatch()
    {
        $this->render('system-mismatch', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => true,
        ]);
    }

    /**
     * Simulation is RU only
     */
    public function actionLegacyAndTerms($mode, $type, $invite_id)
    {
        $invite = Invite::model()->findByPk($invite_id);

        if ($invite->status == Invite::STATUS_PENDING) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s ещё не одобрено Вами.',
                $invite->getCompanyOwnershipType(),
                ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
            ));

            $this->redirect('/dashboard');
        }

        if ($invite->status == Invite::STATUS_COMPLETED || $invite->status == Invite::STATUS_IN_PROGRESS) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s уже использовано для запуска симуляции.',
                $invite->getCompanyOwnershipType(),
                ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
            ));

            $this->redirect('/dashboard');
        }

        if ($invite->status == Invite::STATUS_DECLINED) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s было отклонено.',
                $invite->getCompanyOwnershipType(),
                ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
            ));

            $this->redirect('/dashboard');
        }

        if ($invite->status == Invite::STATUS_EXPIRED) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s просрочено.',
                $invite->getCompanyOwnershipType(),
                ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
            ));

            $this->redirect('/dashboard');
        }

        // for invites to unregistered (when invitation had been send) users, receiver_id is NULL
        // fix (NULL) receiver_id to make sure that simulation can start
        $invite->receiver_id = Yii::app()->user->data()->id;
        $invite->update(false, ['receiver_id']);

        $this->render('legacy_and_terms', [
            'mode'      => $mode,
            'type'      => $type,
            'invite_id' => $invite_id,
        ]);
    }

    public function actionTerms()
    {
        $this->renderPartial('terms');
    }

    public function actionCharts()
    {
        $this->render('charts');
    }

    public function actionFeedback()
    {
        if (!Yii::app()->request->getIsAjaxRequest()) {
            $this->redirect('/');
        }

        $user = Yii::app()->user->data();

        if (Yii::app()->request->getParam('Feedback')) {
            $model = new Feedback();
            $model->addition = (new DateTime())->format("Y-m-d H:i:s");
            $model->attributes = Yii::app()->request->getParam('Feedback');
            if ($user->profile && $user->profile->email && empty($model->email)) {
                $model->email = strtolower($user->profile->email);
            }

            $errors = CActiveForm::validate($model, null, false);
            if (Yii::app()->request->getParam('ajax') === 'feedback-form') {
                echo $errors;
            } elseif (!$model->hasErrors()) {
                $model->save();
                $inviteEmailTemplate = Yii::app()->params['emails']['newFeedback'];

                $body = (new CController("DebugController"))->renderPartial($inviteEmailTemplate, [
                    'email' => strtolower($model->email),
                    'theme' => $model->theme,
                    'message'=>$model->message
                ], true);

                $mail = new SiteEmailOptions();
                $mail->from = Yum::module('registration')->registrationEmail;
                $mail->to = 'help@skiliks.com';
                $mail->subject = 'Новый отзыв';
                $mail->body = $body;
                $mail->embeddedImages = [
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
                    ];
                MailHelper::addMailToQueue($mail);
                Yii::app()->user->setFlash('success', 'Спасибо за ваш отзыв!');
            }
        }
    }

    public function actionDragAndDropPrototype()
    {
        $this->layout = false;
        $this->render('drag_and_drop_prototype');
    }

    public function actionFormErrorsStandard()
    {
        $invite = new Invite();
        $passwordForm = new YumUserChangePassword;
        $passwordForm2 = new YumUserChangePassword;

        $passwordForm->verifyPassword = 1;
        $passwordForm2->verifyPassword = 2;
        $passwordForm2->currentPassword = 3;
        $passwordForm2->password = 1;

        $passwordForm->addError('currentPassword', Yii::t('site', 'Wrong current password'));

        $invite->validate();
        $passwordForm->validate();
        $passwordForm2->validate();

        $vacancies = [];
        $vacancyList = Vacancy::model()->findAllByAttributes(['user_id' => Yii::app()->user->id]);
        foreach ($vacancyList as $vacancy) {
            $vacancies[$vacancy->id] = Yii::t('site', $vacancy->label);
        }

        $this->layout = 'site_standard';

        $this->render('//new/form_errors_standard', [
            'invite'        => $invite,
            'vacancies'     => $vacancies,
            'passwordForm'  => $passwordForm,
            'passwordForm2' => $passwordForm2,
        ]);
    }

    /**
     *
     */
    public function actionProductNew()
    {
        $this->layout = 'site_standard';
        $this->render('//new/product');
    }


    /**
     *
     */
    public function actionTeamNew()
    {
        $this->layout = 'site_standard';
        $this->render('//new/team');
    }

    /**
     *
     */
    public function actionOldBrowserNew()
    {
        $this->layout = 'site_standard';
        $this->render('//new/oldBrowser', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => true,
        ]);
    }

    public function actionHomeNew()
    {
        $this->layout = 'site_standard';
        /* @var $user YumUser */
        $this->render('//new/home', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => false,
        ]);
    }

    /**
     *
     */
    public function actionTariffsNew()
    {
        $user = Yii::app()->user;
        $user = $user->data();

        $this->layout = 'site_standard';

        $this->render('//new/tariffs', [
            'tariffs' => Tariff::model()->findAll('',['order' => 'order ASD']), 'user' => $user
        ]);
    }

    public function actionAddUserSubscription()
    {
        $email = Yii::app()->request->getParam('email', false);
        $result = UserService::addUserSubscription($email);

        $this->sendJSON($result);
    }
}
