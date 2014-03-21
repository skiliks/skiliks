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
        $this->layout = 'site_standard_2';

        $this->addSiteJs('_page-homepage.js');
        $this->addSiteJs('_start_demo.js');

        $this->addSiteCss('pages/homepage-1280.css');
        $this->addSiteCss('pages/homepage-1024.css');
        $this->addSiteCss('partials/system-mismatch.css');

        /* @var $user YumUser */
        $this->render('home', [
            'assetsUrl'          => $this->getAssetsUrl(),
            'userSubscribed'     => false,
            'httpUserAgent'      => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'HTTP_USER_AGENT скрыт.',
            'isSkipBrowserCheck' => (int)Yii::app()->params['public']['isSkipBrowserCheck'],
        ]);
    }

    /**
     *
     */
    public function actionTeam()
    {
        $this->layout = '//layouts/site_standard_2';

        $this->addSiteJs('_start_demo.js');
        $this->addSiteCss('pages/team-1280.css');
        $this->addSiteCss('pages/team-1024.css');

        $this->render('team');
    }

    /**
     *
     */
    public function actionProduct()
    {
        $this->layout = 'site_standard_2';

        $this->addSiteJs('libs/d3.v3.js');
        $this->addSiteJs('libs/charts.js');

        $this->addSiteJs('_page-product.js');
        $this->addSiteJs('_start_demo.js');
        $this->addSiteJs('_simulation-details-popup.js');

        $this->addSiteCss('pages/product-1280.css');
        $this->addSiteCss('pages/product-1024.css');

        $this->addSiteCss('partials/simulation-details-1280.css');
        $this->addSiteCss('partials/simulation-details-1024.css');

        $this->render('product');
    }

    /**
     *
     */
    public function actionProductDiagnostic()
    {
        $this->layout = 'site_standard_2';

        $this->addSiteJs('libs/d3.v3.js');
        $this->addSiteJs('libs/charts.js');

        $this->addSiteJs('_page-product.js');
        $this->addSiteJs('_start_demo.js');
        $this->addSiteJs('_simulation-details-popup.js');

        $this->addSiteCss('pages/product-1280.css');
        $this->addSiteCss('pages/product-1024.css');

        $this->addSiteCss('partials/simulation-details-1280.css');
        $this->addSiteCss('partials/simulation-details-1024.css');

        $this->render('product-diagnostic');
    }

    /**
     *
     */
    public function actionTariffs()
    {
        if($this->user->isAuth()) {
            Yii::app()->setLanguage("ru");
        }

        $this->layout = '//layouts/site_standard_2';

        $this->addSiteCss('/pages/prices.css');
        $this->addSiteJs('_start_demo.js');

        $this->render('tariffs');
    }

    /**
     *
     */
    public function actionSystemMismatch()
    {
        $this->layout = 'site_standard_2';

        $this->addSiteCss('partials/system-mismatch.css');
        $this->addSiteJs('_start_demo.js');

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
            $feedback = new Feedback();
            $feedback->addition = (new DateTime())->format("Y-m-d H:i:s");
            $feedback->attributes = Yii::app()->request->getParam('Feedback');
            if ($user->profile && $user->profile->email && empty($feedback->email)) {
                $feedback->email = strtolower($user->profile->email);
            }

            $errors = CActiveForm::validate($feedback, null, false);
            if (Yii::app()->request->getParam('ajax') === 'feedback-form') {
                echo $errors;
            } elseif (false === $feedback->hasErrors()) {
                $feedback->save();

                $mailOptions          = new SiteEmailOptions();
                $mailOptions->from    = Yum::module('registration')->registrationEmail;
                $mailOptions->to      = 'help@skiliks.com';
                $mailOptions->subject = 'Новый отзыв с домена '.Yii::app()->params['server_domain_name'];
                $mailOptions->h1      = '';
                $mailOptions->text1   = '
                    <h3 style="color:#626250;font-family:Tahoma, Geneva, sans-serif;font-size:28px;margin:0 0 15px 0;padding:0;">
                        Email:
                    </h3>
                    <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                        ' . strtolower($feedback->email) . '
                    </p>
                    <h3 style="color:#626250;font-family:Tahoma, Geneva, sans-serif;font-size:28px;margin:0 0 15px 0;padding:0;">
                        Тема:
                    </h3>
                    <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                        ' . $feedback->theme . '
                    </p>
                    <h3 style="color:#626250;font-family:Tahoma, Geneva, sans-serif;font-size:28px;margin:0 0 15px 0;padding:0;">
                        Сообщение:
                    </h3>
                    <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                        ' . $feedback->message . '
                    </p>
                ';

                UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_ANJELA);

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

    public function actionAddUserSubscription()
    {
        $email = Yii::app()->request->getParam('email', false);
        $result = UserService::addUserSubscription($email);

        $this->sendJSON($result);
    }
}
