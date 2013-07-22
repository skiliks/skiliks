<?php

class PagesController extends SiteBaseController
{
    public $is_test = false;

    public function beforeAction($action)
    {
        $user = Yii::app()->user;
        if (!$user->isGuest &&
            $user->data()->account_corporate &&
            !$user->data()->account_corporate->is_corporate_email_verified
        ) {
            $this->redirect('/userAuth/afterRegistration');
        }

        if (!$user->isGuest &&
            $user->data()->isActive() &&
            !$user->data()->isHasAccount()
        ) {
            $this->redirect('/registration/choose-account-type');
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        /* @var $user YumUser */
        $this->render('home', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => false,
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
        $user = Yii::app()->user;
        $user = $user->data();

        $this->render('tariffs', [
            'tariffs' => Tariff::model()->findAll('',['order' => 'order ASD']), 'user' => $user
        ]);
    }

    /**
     *
     */
    public function actionOldBrowser()
    {
        $this->render('oldBrowser', [
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

            $this->redirect('/simulations');
        }

        if ($invite->status == Invite::STATUS_COMPLETED || $invite->status == Invite::STATUS_STARTED) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s уже использовано для запуска симуляции.',
                $invite->getCompanyOwnershipType(),
                ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
            ));

            $this->redirect('/simulations');
        }

        if ($invite->status == Invite::STATUS_DECLINED) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s было отклонено.',
                $invite->getCompanyOwnershipType(),
                ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
            ));

            $this->redirect('/simulations');
        }

        if ($invite->status == Invite::STATUS_EXPIRED) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s просрочено.',
                $invite->getCompanyOwnershipType(),
                ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
            ));

            $this->redirect('/simulations');
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
                $model->email = $user->profile->email;
            }

            $errors = CActiveForm::validate($model, null, false);
            if (Yii::app()->request->getParam('ajax') === 'feedback-form') {
                echo $errors;
            } elseif (!$model->hasErrors()) {
                $model->save();
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
        $vacancyList = Vacancy::model()->byUser(Yii::app()->user->id)->findAll();
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
