<?php

class AdminInvoicesController extends BaseAdminController {

    /**
     * Список заказов
     */
    public function actionOrders()
    {
        if (false == Yii::app()->user->data()->can('orders_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $isEmptyFilters =
            false === Yii::app()->request->getParam('email', false)
            && false === Yii::app()->request->getParam('cash', false)
            && false === Yii::app()->request->getParam('robokassa', false)
            && false === Yii::app()->request->getParam('notDone', false)
            && false === Yii::app()->request->getParam('isTestPayment', false)
            && false === Yii::app()->request->getParam('isRealPayment', false);
        // если вс фильтры пусты - то надо задать значение по умолчанию
        // в true все чекбоксы кроме isTestPayment.

        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

        $request_uri = $_SERVER['REQUEST_URI'];

        $disableFilters = Yii::app()->request->getParam("disable_filters", null);
        // adding session
        $session = new CHttpSession();

        // taking up address to

        if( null !== $disableFilters) {
            $session["order_address"] = null;
        }

        if($request_uri == "/admin_area/orders" && $session["order_address"] != null && $session["order_address"] != $request_uri) {
            $this->redirect($session["order_address"]);
        }

        $session["order_address"] = $request_uri;

        $criteria = new CDbCriteria;

        $criteria->join = "JOIN profile ON profile.user_id = t.user_id";

        // applying filters
        $filterEmail = strtolower(Yii::app()->request->getParam('email', null));

        if($filterEmail !== null) {
            $filterEmail = trim($filterEmail);
            $criteria->addSearchCondition("profile.email", $filterEmail);
        }

        // appying payment method filters
        $filterCash = Yii::app()->request->getParam('cash', $isEmptyFilters);
        $filterRobokassa = Yii::app()->request->getParam('robokassa', $isEmptyFilters);

        if($filterCash !== false && $filterRobokassa === false) {
            $criteria->compare("payment_system", 'cash');
        }
        elseif($filterCash === false && $filterRobokassa !== false) {
            $criteria->compare("t.payment_system", 'robokassa');
        }
        // if both are not null we taking everything

        // applying done / not done filters
        $done = Yii::app()->request->getParam('done', $isEmptyFilters);
        $notDone = Yii::app()->request->getParam('notDone', $isEmptyFilters);

        if($done !== false && $notDone === false) {
            $criteria->addCondition("t.paid_at IS NOT NULL");
        } elseif ($done === false && $notDone !== false) {
            $criteria->addCondition("t.paid_at IS NULL");
        }
        // if both are not null we taking everything

        // applying done / not done filters
        $isTestPayment = Yii::app()->request->getParam('isTestPayment', false);
        $isRealPayment = Yii::app()->request->getParam('isRealPayment', $isEmptyFilters);

        if ('on' == $isTestPayment) {
            $isTestPayment = true;
        }

        if ('on' == $isRealPayment) {
            $isRealPayment = true;
        }

        if($isTestPayment && false == $isRealPayment) {
            $criteria->addCondition("t.is_test_payment = 1");
        } elseif (false == $isTestPayment && $isRealPayment) {
            $criteria->addCondition("t.is_test_payment = 0");
        } elseif (false == $isTestPayment && false == $isRealPayment) {
            $criteria->addCondition("t.is_test_payment IS NULL");
        }
        // if both are not null we taking everything

        // setting the form to get it in the view

        // checking if submit button wasn't pushed
        $formSended = Yii::app()->request->getParam('form-send', null);

        $appliedFilters = ["email"           => $filterEmail,
            "robokassa"       => $filterRobokassa,
            "cash"            => $filterCash,
            "done"            => $done,
            "notDone"         => $notDone,
            "isTestPayment"   => $isTestPayment,
            "isRealPayment"   => $isRealPayment,
        ];

        // counting objects to make the pagination
        $totalItems = count(Invoice::model()->findAll($criteria));

        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminInvoices/Orders';
        // pager }

        // building criteria
        $criteria->order = "created_at desc" ;
        $criteria->limit = $this->itemsOnPage;
        $criteria->offset = ($page-1)*$this->itemsOnPage;

        $models = Invoice::model()->findAll($criteria);

        $this->layout = '//admin_area/layouts/admin_main';

        $this->render('/admin_area/pages/orders', [
            'models'      => $models,
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage,
            'filters'     => $appliedFilters
        ]);
    }

    /**
     *
     */
    public function actionCompleteInvoice() {
        $invoiceId = Yii::app()->request->getParam('invoice_id');

        $admin = Yii::app()->user->data();

        $criteria = new CDbCriteria();
        $criteria->compare('id', $invoiceId);

        $invoice = Invoice::model()->find($criteria);

        if($invoice !== null && $invoice->paid_at == null) {
            $user = Yii::app()->user->data();
            $invoice_log = new LogPayments();
            $invoice_log->log($invoice, "Попытка отметить счёт как \"Оплаченый\" в админке. Админ ".$user->profile->email.".");

            $initValue = $invoice->user->getAccount()->getTotalAvailableInvitesLimit();

            $invoice->completeInvoice($user->profile->email);

            UserService::logCorporateInviteMovementAdd(sprintf(
                "Принята оплата по счёт-фактуре номер %s. Админ %s.",
                $invoice->id, $admin->profile->email
            ),  $invoice->user->getAccount(), $initValue);

            echo json_encode(["return" => true, "paidAt" => $invoice->paid_at]);
        }
    }

    /**
     *
     */
    public function actionDisableInvoice() {
        $invoiceId = Yii::app()->request->getParam('invoice_id');

        $criteria = new CDbCriteria();
        $criteria->compare('id', $invoiceId);

        $invoice = Invoice::model()->find($criteria);

        if($invoice !== null && $invoice->paid_at != null) {
            $user = Yii::app()->user->data();

            $invoice_log = new LogPayments();
            $initValue = $invoice->user->getAccount()->getTotalAvailableInvitesLimit();

            $invoice_log->log($invoice, "Попытка отметить счёт как \"Не оплаченый\" в админке. Админ ".$user->profile->email.".");
            $invoice->disableInvoice($user->profile->email);

            UserService::logCorporateInviteMovementAdd(sprintf(
                "Банковский перевод признан несостоявшимся. Админ %s (емейл текущего авторизованного в админке пользователя).",
                $user->profile->email
            ),  $invoice->user->getAccount(), $initValue);

            echo json_encode(["return" => true]);
        }

    }

    /**
     * Обновить комментарий к заказу
     */
    public function actionCommentInvoice() {
        $invoiceId = Yii::app()->request->getParam('invoice_id');
        $criteria = new CDbCriteria();
        $criteria->compare('id', $invoiceId);

        $invoice = Invoice::model()->find($criteria);

        if($invoice !== null) {
            $oldComment = $invoice->comment;
            $invoice->comment = (Yii::app()->request->getParam('invoice_comment'));
            $invoice->save();
            $user = Yii::app()->user->data();
            $invoice_log = new LogPayments();
            $invoice_log->log($invoice, sprintf(
                '%s изменил комментарий с "%s" на "%s".',
                $user->profile->email,
                $oldComment,
                $invoice->comment
            ));
            echo json_encode(["return" => true]);
        }
    }

    /**
     * Обновляет стоимость заказа getParam('invoice_id') согластно getParam('invoice_amount')
     */
    public function actionInvoicePriceUpdate() {
        $invoiceId = Yii::app()->request->getParam('invoice_id');
        $criteria = new CDbCriteria();
        $criteria->compare('id', $invoiceId);

        /** @var Invoice $invoice */
        $invoice = Invoice::model()->find($criteria);

        if($invoice !== null) {

            $oldAmount = $invoice->amount;
            $invoice->amount = (float)(str_replace(' ','', Yii::app()->request->getParam('invoice_amount')));
            $invoice->save();
            $user = Yii::app()->user->data();
            $invoice_log = new LogPayments();
            $invoice_log->log($invoice, sprintf(
                "%s изменил цену с %s на %s.",
                $user->profile->email,
                $oldAmount,
                $invoice->amount
            ));
            echo json_encode(["return" => true]);
        }
    }

    /**
     *
     */
    public function actionGetInvoiceLog() {
        $invoiceId = Yii::app()->request->getParam('invoice_id');

        $logs = LogPayments::model()->findAll([
            'order'    => 'created_at DESC',
            'condition' => ' invoice_id = :invoice_id ',
            'params' => [
                'invoice_id' => $invoiceId
            ]
        ]);
        $returnData = "";
        if(!empty($logs)) {
            $returnData = "<table class=\"table\"><tr><td>Время</td><td>Текст лога</td></tr>";
            foreach($logs as $log) {
                $returnData .= '<tr><td><span style="color: #003bb3;">'.$log->created_at.'</span></td>';
                $returnData .= '<td>'.$log->text.'</td></tr>';
            }
            $returnData .= "</table>";
        }
        echo json_encode(["log" => $returnData]);
    }

    /**
     * Меняет значение invoice->is_test_payment на противоположное
     */
    public function actionOrderToggleIsTest($invoiceId)
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::model()->findByPk($invoiceId);

        if (null !== $invoice) {
            $invoice->is_test_payment = abs($invoice->is_test_payment - 1);
            $invoice->save();

            $label = (1 == $invoice->is_test_payment) ? 'тестовый' : 'реальный' ;

            Yii::app()->user->setFlash('success',
                sprintf(
                    'Заказа #%s конвертирован в "%s".',
                    $invoiceId,
                    $label
                )
            );
        } else {
            Yii::app()->user->setFlash('error', sprintf('Заказа #%s нет в базе данных.', $invoiceId));
        }
    }

    /**
     * @throws Exception
     */
    public function actionOrderChecked() {

        $order_id = Yii::app()->request->getParam('order_id', null);
        /* @var $model Invoice */
        $model = Invoice::model()->findByPk($order_id);
        if(null === $model){
            throw new Exception("Order - {$order_id} is not found!");
        }
        $model->is_verified = Invoice::CHECKED;
        if(false === $model->save(false)){
            throw new Exception("Not save");
        }
        $this->redirect("/admin_area/orders");
    }

    public function actionOrderUnchecked() {

        $order_id = Yii::app()->request->getParam('order_id', null);
        /* @var $model Invoice */
        $model = Invoice::model()->findByPk($order_id);
        if(null === $model){
            throw new Exception("Order - {$order_id} is not found!");
        }
        $model->is_verified = Invoice::UNCHECKED;
        $model->status = Invoice::STATUS_PENDING;
        if(false === $model->save(false)){
            throw new Exception("Not save");
        }
        $this->redirect("/admin_area/orders");
    }

    /**
     * @throws Exception
     */
    public function actionOrderActionStatus() {

        $order_id = Yii::app()->request->getParam('order_id', null);
        $status = Yii::app()->request->getParam('status', null);
        /* @var $model Invoice */
        $model = Invoice::model()->findByPk($order_id);
        if(null === $model && null === $status){
            throw new Exception("Order - {$order_id} is not found!");
        }
        if(in_array($status, $model->getStatuses())){
            $model->status = $status;
            if(false === $model->save(false)){
                throw new Exception("Not save");
            }
        }else{
            throw new Exception("Status not found");
        }
        $this->redirect("/admin_area/orders");
    }
} 