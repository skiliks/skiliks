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

    public function actionDo()
    {
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!Yii::app()->request->getIsAjaxRequest() || !$user->isAuth() || !$user->isCorporate()) {
            $this->redirect('/');
        }

        $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');
        $Tariff = Yii::app()->request->getParam('Tariff');
        $account = $user->account_corporate;

        if (null !== $UserAccountCorporate && null !== $Tariff) {
            $account->preference_payment_method = $method = $UserAccountCorporate['preference_payment_method'];

            if ($method === UserAccountCorporate::PAYMENT_METHOD_INVOICE) {
                $invoice = new Invoice();

                $account->inn                 = $invoice->inn     = $UserAccountCorporate['inn'];
                $account->cpp                 = $invoice->cpp     = $UserAccountCorporate['cpp'];
                $account->bank_account_number = $invoice->account = $UserAccountCorporate['bank_account_number'];
                $account->bic                 = $invoice->bic     = $UserAccountCorporate['bic'];

                $invoice->user_id = $user->id;
                $invoice->tariff_id = $Tariff['id'];
                $invoice->status = Invoice::STATUS_PENDING;

                $errors = CActiveForm::validate($account);

                if (Yii::app()->request->getParam('ajax') === 'payment-form') {
                    echo $errors;
                } elseif (!$account->hasErrors()) {
                    $account->save();
                    $invoice->save();

                    echo 'success';
                }
            }
        }
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
