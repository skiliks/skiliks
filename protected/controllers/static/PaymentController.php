<?php

class PaymentController extends SiteBaseController
{
    const SYSTEM_ERROR = 'Ошибка системы. Обратитесь к владельцам сайта для уточнения причины.';

    public function actionOrder($tariffType = null)
    {
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                '1 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        $this->render('order', [
            'account' => $user->account_corporate,
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
        $account = $user->account_corporate;

        if (null !== $UserAccountCorporate) {
            $account->preference_payment_method = $method = $UserAccountCorporate['preference_payment_method'];

            if ($method === UserAccountCorporate::PAYMENT_METHOD_INVOICE && null !== $Invoice) {
                $invoice = new Invoice();

                $account->inn                 = $invoice->inn     = $Invoice['inn'];
                $account->cpp                 = $invoice->cpp     = $Invoice['cpp'];
                $account->bank_account_number = $invoice->account = $Invoice['account'];
                $account->bic                 = $invoice->bic     = $Invoice['bic'];

                $invoice->user_id = $user->id;
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

            $months = Yii::app()->request->getParam('cash-month-selected');

            if( !isset($months) || $months === null || (int)$months == 0) {
                throw new Exception("Invoice has to be created for at least one month");
            }

            $invoice = new Invoice();
            $invoice->payment_system = "cash";
            $invoice->additional_data = json_encode(["inn"  => $paymentMethod->inn,
                                                     "cpp" => $paymentMethod->cpp,
                                                     "account" => $paymentMethod->account,
                                                     "bic" => $paymentMethod->bic]);
            // setting months that user selected, after it create an invoice and save it
            $invoice->createInvoice($user, $months);

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
                '2 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        $months = Yii::app()->request->getParam('monthSelected');

        if( !isset($months) || $months === null || (int)$months == 0) {
            throw new Exception("Invoice has to be created for at least one month");
        }

        $invoice = new Invoice();
        $invoice->payment_system = "robokassa";
        // setting months that user selected, after it create an invoice and save it
        $invoice->createInvoice($user, $months);

        $robokassa = new RobokassaPaymentMethod();
        $robokassa->setDescription($user, $invoice);
        $formData = $robokassa->generateJsonBackData($invoice);
        echo json_encode($formData);
    }

    public function actionSuccess() {
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                '3 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        Yii::app()->user->setFlash('error', sprintf(
            'Оплата прошла успешно, спасибо'
        ));
        $this->redirect('/dashboard');
    }

    public function actionFail() {
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                '4 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        $invoiceId = Yii::app()->request->getParam('InvId');

        $criteria = new CDbCriteria();
        $criteria->compare('id', $invoiceId);

        $invoice = Invoice::model()->find($criteria);
        if(null != $invoice) {

            Yii::app()->user->setFlash('error', sprintf(
                'Извините, оплата прошла неуспешно'
            ));
            $this->redirect('/static/tariffs');
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


                    $initValue = $invoice->user->getAccount()->getTotalAvailableInvitesLimit();

                    UserService::logCorporateInviteMovementAdd(sprintf("Принята оплата по счёт-фактуре номер %s, на тарифный план %s. Количество доступных симуляций установлено в %s.",
                        $invoice->id, $invoice->tariff->label, $initValue), $invoice->user->getAccount(), 0);
                }
                else throw new Exception("Invoice is not complete");
            }
            else {
                echo $paymentMethod->get_result_key($invoice, Yii::app()->request->getParam('OutSum'));
                Yii::app()->end();
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
                '5 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        Yii::app()->user->setFlash('error', sprintf(
            'Ваш заказ принят, в течении дня вам на почту придёт счёт фактура для оплаты выбранного тарифного плана.'
        ));
        $this->redirect('/dashboard');
    }

}
