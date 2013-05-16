<?php

class PaymentController extends AjaxController
{
    public function actionIndex($tariffType = null)
    {
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            $this->redirect('/');
        }

        $tariff = null === $tariffType ?
           $user->account_corporate->tariff :
           Tariff::model()->findByAttributes(['slug' => $tariffType]);

        if (null === $tariff) {
            $this->redirect('/');
        }

        $this->renderPartial('index', [
            'account' => $user->account_corporate,
            'tariff' => $tariff
        ]);
    }

    public function actionChangeTariff($type = null)
    {
        $user = Yii::app()->user->data();
        $type = Yii::app()->request->getParam('type');

        $tariff = Tariff::model()->findByAttributes(['slug' => $type]);

        // is Tariff exist
        if (null == $tariff) {
            $this->redirect('/static/tariff');
        }

        // in release 1 - user can use "Lite" plan only
        if (Tariff::SLUG_LITE != $type) {
            $this->redirect('/static/tariff');
        }

        // is user authenticated
        if (false == $user->isAuth()) {
            $this->redirect('/registration');
        }

        // is Anonymous
        if ($user->isAnonymous()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
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
                $this->redirect('/profile/corporate/tariff');
            }

            // update account tariff
            $user->getAccount()->setTariff($tariff);
            $user->getAccount()->save();

            if($user->getAccount()->tariff_id == $tariff->id) {
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
}
