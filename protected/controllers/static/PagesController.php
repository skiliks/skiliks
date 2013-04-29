<?php

class PagesController extends AjaxController
{
    public $user;
    public $signInErrors = [];

    public function actionIndex()
    {

        $user = Yii::app()->user->data();
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
    public function actionContacts()
    {
        $this->render('contacts');
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

    public function actionChangeTariff($type = null)
    {
        $user = Yii::app()->user->data();
        $type = Yii::app()->request->getParam('type');

        $tariff = Tariff::model()->findByAttributes(['slug' => $type]);

        // is Tariff exist
        if (null == $tariff) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тарифа "%s" не существует.',
                $type
            ));
            $this->redirect('/static/tariff');
        }

        // in release 1 - user can use "Lite" plan only
        if (Tariff::SLUG_LITE != $type) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тариф "%s" не доступен к выбору. <br/><br/>Используйте форму обтатной связи чтоб всязаться с нами и сменить тарыфный план на интересующий Вас.',
                $tariff->label
            ));
            $this->redirect('/static/tariff');
        }

        // is user authenticated
        if (false == $user->isAuth()) {
            $this->redirect('/registration');
        }

        // is Anonymous
        if ($user->isAnonymous()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Укажите тип своего аккаунта.<br/><br/>
                Тарифные планы применимы<br/> только для корпоративных аккаунтов. <br/><br/>
                Пользователи-соискатели могут проходить симуляцию только по приглашениям и демонстрационную версию симуляции.'
            ));
            $this->redirect('/registration/choose-account-type');
        }

        // is Personal account
        if ($user->isPersonal()) {
            Yii::app()->user->setFlash('error',
                "Выбор тарифа доступен только для корпоративных пользователей.<br/><br/>  ".
                "Вы можете <a href='/logout/registration'>зарегистрировать</a> корпоративный профиль"
            );
            $this->redirect('/static/tariffs');
        }

        // is Corporate account
        if($user->isCorporate()) {

            // prevent cheating
            if($user->getAccount()->tariff_id == $tariff->id) {
                Yii::app()->user->setFlash('error', sprintf(
                    'Для Вашего профиля уже активирован тарифный план "%s".',
                    $tariff->label
                ));
                $this->redirect('/profile/corporate/tariff');
            }

            // update account tariff
            $user->getAccount()->setTariff($tariff);
            $user->getAccount()->save();

            if($user->getAccount()->tariff_id == $tariff->id) {
                //@popup
                //Yii::app()->user->setFlash('success', sprintf(
                //    'Тарифный план "%s" активирован для вашего профиля.',
                //    $tariff->label
                //));
                $this->redirect('/profile/corporate/tariff');
            }

            $this->redirect("/profile/corporate/tariff");
        }

        // other undefined errors
        Yii::app()->user->setFlash('error', sprintf(
            "Ошибка системы. Обратитесь в владельцам сайта для уточнения причины."
        ));
        $this->redirect('/static/tariff');
    }

    /**
     *
     */
    public function actionComingSoonSuccess()
    {
        $this->render('home', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => true,
        ]);
    }

    /**
     *
     */
    public function actionAddUserSubscription()
    {
        $email = Yii::app()->request->getParam('email', false);
        $result = UserService::addUserSubscription($email);

        $this->sendJSON($result);
    }

    /**
     *
     */
    public function actionBadBrowser()
    {
        $this->render('badBrowser', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => true,
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

    public function actionCharts()
    {
        $this->render('charts');
    }

    public function actionFeedback()
    {
        if (!Yii::app()->request->getIsAjaxRequest()) {
            $this->redirect('/');
        }

        if (Yii::app()->request->getParam('Feedback')) {
            $model = new Feedback();
            $errors = CActiveForm::validate($model);

            if (Yii::app()->request->getParam('ajax') === 'feedback-form') {
                echo $errors;
            } elseif (!$model->hasErrors()) {
                $model->save();
                Yii::app()->user->setFlash('success', 'Спасибо за ваш отзыв!');
            }
        }
    }
}
