<?php

class AdminPagesController extends SiteBaseController {

    public $itemsOnPage = 100;

    public function beforeAction($action) {

        $public = ['Login'];
        $user = Yii::app()->user->data();
        if(in_array($action->id, $public)){
            return true;
        }elseif(!$user->isAuth()){
            $this->redirect('/registration');
        }elseif(!$user->isAdmin()){
            $this->redirect('/dashboard');
        }
        return true;
    }

    public function actionDashboard() {

        $this->layout = '//admin_area/layouts/admin_main';
        Yii::app()->user->setFlash('error', "Data saved!");
        $this->render('/admin_area/pages/dashboard', ['user'=>$this->user]);

    }

    public function actionLogin() {

        $form = Yii::app()->request->getParam('YumUserLogin', null);
        $model = new YumUserLogin('login_admin');
        if(null !== $form) {
            $model->setAttributes($form);
            if($model->loginByUsernameAdmin()){
                $model->user->authenticate($form['password']);
                $this->redirect('/admin_area/dashboard');
            }
        }
        $this->layout = '//admin_area/layouts/login';
        $this->render('/admin_area/pages/login', ['model'=>$model]);

    }

    public function actionLogout() {

        if (false === Yii::app()->user->isGuest) {
            Yii::app()->user->logout();
        }
        $this->redirect('/');
    }

    public function actionInvites()
    {

        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

        $criteria = new CDbCriteria;
        $totalItems = Invite::model()->count($criteria);
        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/Invites';
        // pager }

        $models = Invite::model()->findAll([
            "order"  => "updated_at desc",
            "limit"  => $this->itemsOnPage,
            "offset" => ($page-1)*$this->itemsOnPage
        ]);
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/invites', [
            'models'      => $models,
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage
        ]);

    }

    public function actionInvitesSave() {

        $this->layout = false;
        $models = Invite::model()->findAll([
            "order" => "updated_at desc",
            "limit"  => $this->itemsOnPage,
            "offset" => ($page-1)*$this->itemsOnPage
        ]);
        $csv = "ID-симуляции;";
        $csv .= "Email работодателя;";
        $csv .= "Email соискателя;";
        $csv .= "ID инвайта;";
        $csv .= "Статус инвайта;";
        $csv .= "Время начала симуляции;";
        $csv .= "Время конца симуляции;";
        $csv .= "Тип (название) основного сценария;";
        $csv .= "Оценка\r\n";
        foreach($models as $model) {
            $csv .= (empty($model->simulation->id)?'Не найден':$model->simulation->id).';';
            $csv .= (empty($model->ownerUser->profile->email))?'Не найден':$model->ownerUser->profile->email.';';
            $csv .=(empty($model->receiverUser->profile->email))?'Не найден':$model->receiverUser->profile->email.';';
            $csv .=$model->id.';';
            $csv .=$model->getStatusText().';';
            $csv .=(empty($model->simulation->start)?'---- -- -- --':$model->simulation->start).';';
            $csv .=(empty($model->simulation->end)?'---- -- -- --':$model->simulation->end).';';
            $csv .=(empty($model->scenario->slug)?'Нет данных':$model->scenario->slug).';';
            $csv .=$model->getOverall()."\r\n";
        }
        header("Content-type: csv/plain");
        header("Content-Disposition: attachment; filename=invites.csv");
        header("Content-length:".(string)(strlen($csv)));
        echo $csv;
    }

    public function actionSimulationDetail() {
        $sim_id = Yii::app()->request->getParam('sim_id', null);
        @ Yii::app()->request->cookies['display_result_for_simulation_id'] =
            new CHttpCookie('display_result_for_simulation_id', $sim_id);

        $this->redirect('/dashboard');
    }

    public function actionGetBudget() {
        $this->layout = false;
        $sim_id = Yii::app()->request->getParam('sim_id', null);
        $simulation = Simulation::model()->findByPk($sim_id);

        // check document {
        $documentTemplate = $simulation->game_type->getDocumentTemplate([
            'code' => 'D1'
        ]);

        if ($documentTemplate === null) {
            throw new Exception("Файл не найден");
        }

        /** @var MyDocument $document */
        $document = MyDocument::model()->findByAttributes([
            'template_id' => $documentTemplate->id,
            'sim_id' => $sim_id
        ]);

        $scData = $document->getSheetList();
        $filePath = tempnam('/tmp', 'excel_');
        ScXlsConverter::sc2xls($scData, $filePath);

        if (file_exists($filePath)) {
            $xls = file_get_contents($filePath);
        } else {
            throw new Exception("Файл не найден");
        }

        $filename = $sim_id . '_' . $documentTemplate->fileName;
        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $xls;
    }

    public function actionResetInvite() {

        $invite_id = Yii::app()->request->getParam('invite_id', null);
        $invite = Invite::model()->findByPk($invite_id);
        /** @var Invite $invite */
        if (empty($invite)) {
            throw new LogicException('Invite does not exist');
        }

        $result = $invite->resetInvite();
        if(false === $result){
            throw new LogicException("The operation is not successful");
        }

        $this->redirect("/admin_area/invites");
    }

    public function actionOrders()
    {
        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

        $criteria = new CDbCriteria;
        $totalItems = Invoice::model()->count($criteria);
        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/Orders';
        // pager }

        $models = Invoice::model()->findAll([
            "order" => "updated_at desc",
            "limit"  => $this->itemsOnPage,
            "offset" => ($page-1)*$this->itemsOnPage
        ]);

        $this->layout = '//admin_area/layouts/admin_main';

        $this->render('/admin_area/pages/orders', [
            'models'      => $models,
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage
        ]);

    }

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

    /**
     * Chande invite status
     * @throws Exception
     */
    public function actionInviteActionStatus() {

        $invite_id = Yii::app()->request->getParam('invite_id', null);
        $status = Yii::app()->request->getParam('status', null);

        /* @var $model Invite */
        $invite = Invite::model()->findByPk($invite_id);

        if (null === $invite && null === $status) {
            throw new Exception("Invite - {$invite_id} is not found!");
        }

        if ( isset(Invite::$statusText[$status])) {
            $invite->status = $status;
            if(false === $invite->save(false)){
                throw new Exception("Not saved");
            }
            InviteService::logAboutInviteStatus($invite, 'invite : updated : admin');
        } else {
            throw new Exception("Status not found");
        }

        $this->redirect("/admin_area/invites");
    }

    public function actionInviteCalculateTheEstimate() {

        $simId = Yii::app()->request->getParam('sim_id', null);
        $email = str_replace(' ', '+', Yii::app()->request->getParam('email', null));
        SimulationService::CalculateTheEstimate($simId, $email);

        $this->redirect("/admin_area/invites");
    }

    public function actionSiteLogs() {
        $invite_id = Yii::app()->request->getParam('invite_id', null);
        $invite = Invite::model()->findByPk($invite_id);

        $logInvite     = LogInvite::model()->findAllByAttributes(['invite_id' => $invite_id]);
        $logSimulation = LogSimulation::model()->findAllByAttributes(['invite_id' => $invite_id]);

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/invite_site_logs_table', [
            'logInvite'     => $logInvite,
            'logSimulation' => $logSimulation,
        ]);
    }

    /**
     *
     */
    public function actionSimSiteLogs() {
        $simId = Yii::app()->request->getParam('sim_id', null);
        $logSimulation = LogSimulation::model()->findAllByAttributes(['sim_id' => $simId]);

        $this->pageTitle = sprintf('Админка: Лог действий с симуляцией %s на сайте', $simId);
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/simulation_site_logs_table', [
            'logSimulation' => $logSimulation,
        ]);
    }

    /**
     *
     */
    public function actionSimulations()
    {
        $invitesRawArray = Invite::model()->findAll();
        $invites = [];
        foreach ($invitesRawArray as $element) {
            $invites[$element->simulation_id] = $element->id;
        }

        $this->pageTitle = 'Админка: Список симуляций в БД';
        $this->layout = '//admin_area/layouts/admin_main';

        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

        $criteria = new CDbCriteria;
        $totalItems = Simulation::model()->count($criteria);
        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/Simulations';
        // pager }

        $simulations = Simulation::model()->findAll([
            'order' => 'id DESC',
            'offset' => ($page-1) * $this->itemsOnPage,
            'limit'  => $this->itemsOnPage
        ]);

        $this->render('/admin_area/pages/simulations_table', [
            'simulations' => $simulations,
            'invites'     => $invites,
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage
        ]);
    }

    /**
     *
     */
    public function actionUsersList()
    {
        $this->pageTitle = 'Админка: Список пользователей';
        $this->layout = '//admin_area/layouts/admin_main';

        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

        $criteria = new CDbCriteria;
        $totalItems = YumProfile::model()->count($criteria);
        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/UsersList';
        // pager }

        $this->render('/admin_area/pages/users_table', [
            'profiles' => YumProfile::model()->findAll([
                "limit"  => $this->itemsOnPage,
                "offset" => ($page-1)*$this->itemsOnPage
            ]),
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage
        ]);
    }

    /**
     *
     */
    public function actionCorporateAccountList()
    {
        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

        $criteria = new CDbCriteria;
        $totalItems = UserAccountCorporate::model()->count($criteria);
        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/CorporateAccountList ';
        // pager }

        $this->pageTitle = 'Админка: Список корпоративных аккаунтов';
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/corporate_accounts_table', [
            'accounts' => UserAccountCorporate::model()->findAll([
                "limit"  => $this->itemsOnPage,
                "offset" => ($page-1)*$this->itemsOnPage
            ]),
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage
        ]);
    }

    /**
     *
     */
    public function actionCorporateAccountInviteLimitLogs()
    {
        $id = Yii::app()->request->getParam('id', null);

        $account = UserAccountCorporate::model()->findByAttributes(['user_id' => $id]);
        $logs = LogAccountInvite::model()->findAllByAttributes([
            'user_id' => Yii::app()->request->getParam('id', null),
        ]);

        $this->pageTitle = 'Админка: Движение проглашений в корпоративном аккаунте # '.$id;
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/corporate_account_invite_limit_logs_table', [
            'logs'    => $logs,
            'account' => $account,
        ]);
    }

    /**
     * @param $userId
     */
    public function actionUpdatePassword($userId)
    {
        $siteUser = YumUser::model()->findByPk($userId);

        if (null === $siteUser) {
            Yii::app()->user->setFash('Такого пользователя для сайта не существеут.');
            $this->redirect('/admin_area/users');
        }
    }
}