<?php

class PaymentController extends SiteBaseController
{
    public function actionOrder($tariffType = null)
    {
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }


        if($user->getInvitesLeft() > 0) {
            Yii::app()->user->setFlash('error', sprintf(
                'У вас ещё остались симуляции. Пожалуйста, используйте их, вы сможете сменить тарифный план после.'
            ));
            $this->redirect('/dashboard');
        }

        $tariff = null === $tariffType ?
           $user->account_corporate->tariff :
           Tariff::model()->findByAttributes(['slug' => $tariffType]);

        if (null === $tariff) {
            Yii::app()->user->setFlash('error', sprintf(
                'Ошибка системы. Обратитесь в владельцам сайта для уточнения причины.'
            ));
            $this->redirect('/');
        }

        $this->render('order', [
            'account' => $user->account_corporate,
            'tariff' => $tariff,
            'paymentMethodCash'      => new CashPaymentMethod(),
            'paymentMethodRobokassa' => new RobokassaPaymentMethod()
        ]);
    }

    public function actionDo()
    {
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!Yii::app()->request->getIsAjaxRequest() || !$user->isAuth() || !$user->isCorporate()) {
            echo 'false';
            Yii::app()->end();
        }

        $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');
        $Invoice = Yii::app()->request->getParam('Invoice');
        $Tariff = Yii::app()->request->getParam('Tariff');
        $account = $user->account_corporate;

        if (null !== $UserAccountCorporate && null !== $Tariff) {
            $account->preference_payment_method = $method = $UserAccountCorporate['preference_payment_method'];

            if ($method === UserAccountCorporate::PAYMENT_METHOD_INVOICE && null !== $Invoice) {
                $invoice = new Invoice();

                $account->inn                 = $invoice->inn     = $Invoice['inn'];
                $account->cpp                 = $invoice->cpp     = $Invoice['cpp'];
                $account->bank_account_number = $invoice->account = $Invoice['account'];
                $account->bic                 = $invoice->bic     = $Invoice['bic'];

                $invoice->user_id = $user->id;
                $invoice->tariff_id = $Tariff['id'];
                $invoice->status = Invoice::STATUS_PENDING;

                $errors = CActiveForm::validate($invoice);

                if (Yii::app()->request->getParam('ajax') === 'payment-form') {
                    echo $errors;
                } elseif (!$account->hasErrors()) {
                    $account->save();
                    $invoice->save();

                    echo sprintf(
                        Yii::t('site', 'Thanks for your order, Invoice was sent to %s. Plan will be available upon receipt of payment'),
                        $user->profile->email
                    );
                }
            }
        } else {
            echo 'false';
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
            $user->getAccount()->setTariff($tariff, true);

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

    // -----------

    public function actionOrderNew($tariffType = null)
    {
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        $tariff = null === $tariffType ?
            $user->account_corporate->tariff :
            Tariff::model()->findByAttributes(['slug' => $tariffType]);

        if (null === $tariff) {
            Yii::app()->user->setFlash('error', sprintf(
                'Ошибка системы. Обратитесь в владельцам сайта для уточнения причины.'
            ));
            $this->redirect('/');
        }

        $invoice = new Invoice();

        $this->layout = 'site_standard';

        $this->render('//new/order', [
            'account' => $user->account_corporate,
            'invoice' => $invoice,
            'tariff' => $tariff
        ]);
    }

    public function actionDoCashPayment() {

        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!Yii::app()->request->getIsAjaxRequest() || !$user->isAuth() || !$user->isCorporate()) {
            echo 'false';
            Yii::app()->end();
        }

        $account = $user->account_corporate;


        $paymentMethod = new CashPaymentMethod();

        $account->inn                 = $paymentMethod->inn     = Yii::app()->request->getParam('inn');
        $account->cpp                 = $paymentMethod->cpp     = Yii::app()->request->getParam('cpp');
        $account->bank_account_number = $paymentMethod->account = Yii::app()->request->getParam('account');
        $account->bic                 = $paymentMethod->bic     = Yii::app()->request->getParam('bic');

        $errors = CActiveForm::validate($paymentMethod);

        if ($errors && $errors != "[]") {
            echo $errors;
        } elseif ($errors == "[]" && !$account->hasErrors()) {
            $account->save();


            $tariffType = Yii::app()->request->getParam('tariff-label');
            $months = Yii::app()->request->getParam('cash-month-selected');

            if( !isset($months) || $months === null || (int)$months == 0) {
                throw new Exception("Invoice has to be created for at least one month");
            }

            $tariff = null === $tariffType ?
                $user->account_corporate->tariff :
                Tariff::model()->findByAttributes(['slug' => $tariffType]);

            if (null === $tariff) {
                Yii::app()->user->setFlash('error', sprintf(
                    'Ошибка системы. Обратитесь в владельцам сайта для уточнения причины.'
                ));
                $this->redirect('/');
            }

            $invoice = new Invoice();
            $invoice->payment_system = "cash";
            $invoice->additional_data = json_encode(["inn"  => $paymentMethod->inn,
                                                     "cpp" => $paymentMethod->cpp,
                                                     "account" => $paymentMethod->account,
                                                     "bic" => $paymentMethod->bic]);
            // setting months that user selected, after it create an invoice and save it
            $invoice->createInvoice($user, $tariff, $months);

            // send booker email
            if($paymentMethod->sendBookerEmail($invoice, $user)) {
                echo "[]";
            }
        }
    }

    public function actionGetRobokassaForm() {

        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        $tariffType = Yii::app()->request->getParam('tariffType');
        $months = Yii::app()->request->getParam('monthSelected');

        if( !isset($months) || $months === null || (int)$months == 0) {
            throw new Exception("Invoice has to be created for at least one month");
        }

        $tariff = null === $tariffType ?
            $user->account_corporate->tariff :
            Tariff::model()->findByAttributes(['slug' => $tariffType]);

        if (null === $tariff) {
            Yii::app()->user->setFlash('error', sprintf(
                'Ошибка системы. Обратитесь в владельцам сайта для уточнения причины.'
            ));
            $this->redirect('/');
        }

        $invoice = new Invoice();
        $invoice->payment_system = "robokassa";
        // setting months that user selected, after it create an invoice and save it
        $invoice->createInvoice($user, $tariff, $months);

        $robokassa = new RobokassaPaymentMethod();
        $robokassa->setDescription($tariff, $user, $invoice);
        $formData = $robokassa->generateJsonBackData($invoice, $tariff);
        echo json_encode($formData);
    }

    public function actionSuccess() {
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        Yii::app()->user->setFlash('error', sprintf(
            'Оплата прошла успешно, спасибо!'
        ));
        $this->redirect('/dashboard');
    }

    public function actionFail() {
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        $invoiceId = Yii::app()->request->getParam('InvId');

        $criteria = new CDbCriteria();
        $criteria->compare('id', $invoiceId);

        $invoice = Invoice::model()->find($criteria);
        if(null != $invoice) {

            Yii::app()->user->setFlash('error', sprintf(
                'Извините, оплата прошла не успешно.'
            ));
            $this->redirect('/payment/order/'.$invoice->tariff->slug);
        }
    }

    public function actionResult() {

        $invoiceId = Yii::app()->request->getParam('InvId');

        $criteria = new CDbCriteria();
        $criteria->compare('id', $invoiceId);


        $invoice = Invoice::model()->find($criteria);

        if($invoice !== null && $invoice->paid_at == null) {
            $invoice_log = new LogPayments();
            $invoice_log->log($invoice, "Получены данные от Robokassa. Данные: Инвойс №" . Yii::app()->request->getParam('InvId') . ", сумма: " . Yii::app()->request->getParam('OutSum') . ", подпись: " . Yii::app()->request->getParam('SignatureValue'));
            $paymentMethod = new RobokassaPaymentMethod();

            if(Yii::app()->request->getParam('SignatureValue') == $paymentMethod->get_result_key($invoice, Yii::app()->request->getParam('OutSum'))) {

                if($invoice->completeInvoice()) {
                    echo "OK".$invoice->id;
                }
                else throw new Exception("Invoice is not complete");
            }
            else {
                echo $paymentMethod->get_result_key($invoice, Yii::app()->request->getParam('OutSum'));
                exit();
            }
        }
    }

    /**
     * function for cash payment method in case of success
     */

    public function actionInvoiceSuccess() {
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        Yii::app()->user->setFlash('error', sprintf(
            'Ваш заказ принят, в течении дня вам на почту придёт счёт фактура для оплаты выбранного тарифного плана.'
        ));
        $this->redirect('/dashboard');
    }

}
