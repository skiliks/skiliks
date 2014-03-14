<?php

class PaymentController extends SiteBaseController
{
    const SYSTEM_ERROR = 'Ошибка системы. Обратитесь к владельцам сайта для уточнения причины.';

    public function actionOrder()
    {
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Оплата доступна корпоративным пользователям. Пожалуйста, <a href="/logout/registration" class="color-428290">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        $this->layout = 'site_standard_2';

        $this->addSiteCss('/pages/order-1280.css');
        $this->addSiteCss('/pages/order-1024.css');
        $this->addSiteJs('_page-payment.js');
        if(0 === (int)Invoice::model()->count('user_id = '.$user->id.' and paid_at is not null')){
            $minSimulationSelected = Price::model()->findByAttributes(['alias'=>'lite'])->from;
        }else{
            $minSimulationSelected = 1;
        }
        $json = [
            'minSimulationSelected' => $minSimulationSelected
        ];
        foreach(Price::model()->findAll() as $price){
            /* @var $price Price */
            $json['prices'][] = [
                'name' => $price->name,
                'to' => $price->to,
                'from' => $price->from,
                'alias' => $price->alias,
                'in_RUB' => $price->in_RUB,
                'in_USD' => $price->in_USD,
            ];

        }
        $this->render('order', [
            'account' => $user->account_corporate,
            'paymentMethodCash'      => new CashPaymentMethod(),
            'paymentMethodRobokassa' => new RobokassaPaymentMethod(),
            'minSimulationSelected' => $minSimulationSelected,
            'paymentData' => $json
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

            $simulation_selected = Yii::app()->request->getParam('simulation-selected');
            if(0 === (int)Invoice::model()->count('user_id = '.$user->id.' and paid_at is not null')){
                $minSimulationSelected = (int)Price::model()->findByAttributes(['alias'=>'lite'])->from;
            }else{
                $minSimulationSelected = 1;
            }
            if( !isset($simulation_selected) || $simulation_selected === null || (int)$simulation_selected < $minSimulationSelected) {
                throw new Exception("Случилась ошибка, поле simulation-selected не валидное");
            }

            $invoice = new Invoice();
            $invoice->payment_system = "cash";
            $invoice->additional_data = json_encode(["inn"  => $paymentMethod->inn,
                                                     "cpp" => $paymentMethod->cpp,
                                                     "account" => $paymentMethod->account,
                                                     "bic" => $paymentMethod->bic]);
            // setting months that user selected, after it create an invoice and save it
            $invoice->createInvoice($user, $simulation_selected);

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
                '2 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration" class="color-428290">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        $simulation_selected = Yii::app()->request->getParam('simulation-selected');
        if(0 === (int)Invoice::model()->count('user_id = '.$user->id.' and paid_at is not null')){
            $minSimulationSelected = (int)Price::model()->findByAttributes(['alias'=>'lite'])->from;
        }else{
            $minSimulationSelected = 1;
        }
        if( !isset($simulation_selected) || $simulation_selected === null || (int)$simulation_selected < $minSimulationSelected) {
            throw new Exception("Случилась ошибка, поле simulation-selected не валидное");
        }

        $invoice = new Invoice();
        $invoice->payment_system = "robokassa";
        // setting months that user selected, after it create an invoice and save it
        $invoice->createInvoice($user, $simulation_selected);

        $robokassa = new RobokassaPaymentMethod();
        $robokassa->setDescription($user, $simulation_selected);
        $formData = $robokassa->generateJsonBackData($invoice);
        echo json_encode($formData);
    }

    public function actionSuccess() {
        $user = Yii::app()->user->data();

        if (!$user->isAuth() || !$user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                '3 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration" class="color-428290">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
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
                '4 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration" class="color-428290">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
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

        /* @var $invoice Invoice */
        $invoice = Invoice::model()->find($criteria);

        if($invoice !== null && $invoice->paid_at == null) {
            $invoice_log = new LogPayments();
            $invoice_log->log($invoice, "Получены данные от Robokassa. Данные: Инвойс №" . Yii::app()->request->getParam('InvId') . ", сумма: " . Yii::app()->request->getParam('OutSum') . ", подпись: " . Yii::app()->request->getParam('SignatureValue'));
            $paymentMethod = new RobokassaPaymentMethod();

            if(Yii::app()->request->getParam('SignatureValue') == $paymentMethod->get_result_key($invoice, Yii::app()->request->getParam('OutSum'))) {

                if($invoice->completeInvoice()) {
                    echo "OK".$invoice->id;


                    $initValue = $invoice->user->getAccount()->getTotalAvailableInvitesLimit();

                    UserService::logCorporateInviteMovementAdd(sprintf("Принята оплата по счёт-фактуре номер %s. Количество доступных симуляций установлено в %s.",
                        $invoice->id, $initValue), $invoice->user->getAccount(), 0);
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
                '5 Тарифные планы доступны корпоративным пользователям. Пожалуйста, <a href="/logout/registration" class="color-428290">зарегистрируйте</a> корпоративный аккаунт и получите доступ.'
            ));
            $this->redirect('/');
        }

        Yii::app()->user->setFlash('error', sprintf(
            'Ваш заказ принят, в течении дня вам на почту придёт счёт фактура для оплаты выбранного тарифного плана.'
        ));
        $this->redirect('/dashboard');
    }

}
