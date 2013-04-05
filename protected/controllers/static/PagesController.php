<?php

class PagesController extends AjaxController
{
    public $user;
    public $signInErrors = [];

    public function actionIndex()
    {
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
        $this->render('tariffs', [
            'tariffs' => Tariff::model()->findAll('',['order' => 'order ASD'])
        ]);
    }

    public function actionChangeTariff()
    {
        $user = Yii::app()->user;
        if (null === $user) {
            $this->redirect('/registration');
        }
        /* @var YumUser $user */
        $user = $user->data();  //YumWebUser -> YumUser
        if (false === $user->isCorporate()) {
            $this->redirect('/');
        }
        $tariff = Tariff::model()->findByAttributes(['label' => "Lite"]);

        if($user->getAccount()->tariff_id == $tariff->id){
            $this->redirect('/');
        } else {
            $user->getAccount()->tariff_id = $tariff->id;
            $user->getAccount()->tariff_activated_at = (new DateTime())->format("Y-m-d H:i:s"); //date('Y-m-d H:i:s');
            $user->getAccount()->tariff_expired_at = (new DateTime())->modify('+30 days')->format("Y-m-d H:i:s"); //date('Y-').(date('m')+1).date('-d H:i:s');
            $user->getAccount()->invites_limit += $tariff->simulations_amount;
            $user->getAccount()->save();
        }



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
        die;
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
