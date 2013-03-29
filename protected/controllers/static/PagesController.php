<?php

class PagesController extends AjaxController
{
    public $user;
    public $signInErrors = [];

    public function actionIndex($_lang = null)
    {
        $this->defineLanguage($_lang);

        $this->render('home', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => false,
        ]);
    }

    /**
     *
     */
    public function actionTeam($_lang = null)
    {
        $this->defineLanguage($_lang);

        $this->render('team');
    }

    /**
     *
     */
    public function actionProduct($_lang = null)
    {
        $this->defineLanguage($_lang);

        $this->render('product');
    }

    /**
     *
     */
    public function actionContacts($_lang = null)
    {
        $this->defineLanguage($_lang);

        $this->render('contacts');
    }


    /**
     *
     */
    public function actionTariffs($_lang = null)
    {
        $this->defineLanguage($_lang);

        $this->render('tariffs', [
            'tariffs' => Tariff::model()->findAll('',['order' => 'order ASD'])
        ]);
    }

    /**
     *
     */
    public function actionComingSoonSuccess($_lang = null)
    {
        $this->defineLanguage($_lang);

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
        $this->defineLanguage($_lang);

        $email = Yii::app()->request->getParam('email', false);
        $result = UserService::addUserSubscription($email);

        $this->sendJSON($result);
        die;
    }

    /**
     *
     */
    public function actionBadBrowser($_lang = null)
    {
        $this->defineLanguage($_lang);

        $this->render('badBrowser', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => true,
        ]);
    }

    /**
     *
     */
    public function actionOldBrowser($_lang = null)
    {
        $this->defineLanguage($_lang);

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
        Yii::app()->setLanguage('ru');

        $invite = Invite::model()->findByPk($invite_id);

        if ($invite->status == Invite::STATUS_PENDING) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s ещё не одобрено Вами.',
                $invite->ownerUser->getAccount()->ownership_type,
                $invite->ownerUser->getAccount()->company_name
            ));

            $this->redirect('/simulations');
        }

        if ($invite->status == Invite::STATUS_COMPLETED || $invite->status == Invite::STATUS_STARTED) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s уже использовано для запуска симуляции.',
                $invite->ownerUser->getAccount()->ownership_type,
                $invite->ownerUser->getAccount()->company_name
            ));

            $this->redirect('/simulations');
        }

        if ($invite->status == Invite::STATUS_DECLINED) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s было отклонено.',
                $invite->ownerUser->getAccount()->ownership_type,
                $invite->ownerUser->getAccount()->company_name
            ));

            $this->redirect('/simulations');
        }

        if ($invite->status == Invite::STATUS_EXPIRED) {

            Yii::app()->user->setFlash('success', sprintf(
                'Приглашение от %s %s просрочено.',
                $invite->ownerUser->getAccount()->ownership_type,
                $invite->ownerUser->getAccount()->company_name
            ));

            $this->redirect('/simulations');
        }

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
}
