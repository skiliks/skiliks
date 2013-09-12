<?php

class AdminPagesController extends SiteBaseController {

    public $itemsOnPage = 100;

    public $developersEmails = [
        "'r.kilimov@gmail.com'",
        "'andrey@kostenko.name'",
        "'personal@kostenko.name'",
        "'a.levina@gmail.com'",
        "'gorina.mv@gmail.com'",
        "'v.logunov@yahoo.com'",
        "'nikoolin@ukr.net'",
        "'leah.levina@gmail.com'",
        "'lea.skiliks@gmail.com'",
        "'andrey3@kostenko.name'",
        "'skiltests@yandex.ru'",
        "'didmytime@bk.ru'",
        "'gva08@yandex.ru'",
        "'tony_acm@ukr.net'",
        "'tony_perfectus@mail.ru'",
        "'N_ninok1985@mail.ru'",
        "'tony.pryanichnikov@gmail.com'",
        "'svetaswork@gmail.com'",
    ];

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

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/dashboard', ['user'=>$this->user]);

    }

    public function actionLiveSimulations() {
        $condition = " `t`.`start` > (NOW() - interval 4 hour) ";

        $full_simulations = Simulation::model()->findAll([
            'condition' => " `game_type`.`slug` = 'full' AND `t`.`start` > (NOW() - interval 4 hour) ",
            'with'=>array(
                'user',
                'invite',
                'game_type',
            ),
            'order'  => " t.start desc",
        ]);


        $lite_simulations = Simulation::model()->findAll([
            'condition' => " `game_type`.`slug` = 'lite' AND `t`.`start` > (NOW() - interval 1 hour) ",
            'with'=>array(
                'user',
                'invite',
                'game_type',
            ),
            'order'  => " t.start desc",
        ]);


        $tutorial_simulations = Simulation::model()->findAll([
            'condition' => " `game_type`.`slug` = 'tutorial' AND `t`.`start` > (NOW() - interval 30 minute) ",
            'with'=>array(
                'user',
                'invite',
                'game_type',
            ),
            'order'  => " t.start desc",
        ]);

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/live_simulations',
            ['user'=>$this->user,
                'full_simulations'     => $full_simulations,
                'lite_simulations'     => $lite_simulations,
                'tutorial_simulations' => $tutorial_simulations
            ]);
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

        $allFilters = $this->getCriteriaInvites();

        $criteria = $allFilters['criteria'];

        $totalItems = Invite::model()->count($criteria);

        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/Invites';
        // pager }

        //$models = Invite::model()->findAll([
        $models = Invite::model()->findAll([
            'condition' => $allFilters['condition'],
            'order'  => "updated_at desc",
            'limit'  => $this->itemsOnPage,
            'offset' => ($page-1)*$this->itemsOnPage
        ]);

        if (count($models) < $this->itemsOnPage) {
            $page = 1; // если результатов фильтрации мало

            $models = Invite::model()->findAll([
                'condition' => $allFilters['condition'],
                'order'  => "updated_at desc",
                'limit'  => $this->itemsOnPage,
                'offset' => ($page-1)*$this->itemsOnPage
            ]);
        }

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/invites', [
            'models'      => $models,
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage,
            'formFilters' => $allFilters['filters'],
            'receiverEmailForFiltration' => isset($allFilters['filters']['filter_email']) ? $allFilters['filters']['filter_email'] : "",
            'invite_id' => isset($allFilters['filters']['invite_id']) ? $allFilters['filters']['invite_id'] : "",
        ]);
    }


    public function getCriteriaInvites() {
        $clear_form = Yii::app()->request->getParam('clear_form');
        $criteria = new CDbCriteria;
        $condition = false;

        $isReloadRequest = -1 < strpos(Yii::app()->request->urlReferrer, '/admin_area/invites');

        // checking if clear form is not null
        if(null !== $clear_form) {
            $filter_form['invite_statuses'] = [
                Invite::STATUS_PENDING     => true,
                Invite::STATUS_ACCEPTED    => true,
                Invite::STATUS_IN_PROGRESS => true,
                Invite::STATUS_COMPLETED   => true,
                Invite::STATUS_EXPIRED     => false,
                Invite::STATUS_DECLINED    => false,
                Invite::STATUS_DELETED     => false,
            ];
        } else {
            // setting up parameters
            $filter_form = Yii::app()->session['admin_filter_form'];

            $condition = '';

            $receiverEmailForFiltration = trim(Yii::app()->request->getParam('receiver-email-for-filtration', null));
            $invite_id = trim(Yii::app()->request->getParam('invite_id', null));
            $exceptDevelopersFiltration = (bool)trim(Yii::app()->request->getParam('except-developers', true));

            // remaking email form
            if ($isReloadRequest) {
                if (null !== $receiverEmailForFiltration) {
                    $filter_form['filter_email'] = $receiverEmailForFiltration;
                }
                else {
                    $filter_form['filter_email'] = "";
                }
            }

            if ($isReloadRequest) {
                if (null !== $invite_id) {
                    $filter_form['invite_id'] = $invite_id;
                }
                else {
                    $filter_form['invite_id'] = "";
                }
            }

            Yii::app()->session['admin_filter_form'] = $filter_form;

            $previousConditionPresent = false;

            // checking if filters are not empty
            if(null != $filter_form && !empty($filter_form)) {

                // setting all filters
                if(isset($filter_form['filter_email']) && $filter_form['filter_email'] != "" ) {
                    $condition .= " email LIKE '%".$filter_form['filter_email']."%' ";
                    $previousConditionPresent = true;
                }
                if(isset($filter_form['invite_id']) && $filter_form['invite_id'] && $filter_form['invite_id'] != "" ) {
                    if($condition !== "") {
                        $condition .= " AND ";
                    }
                    $condition .= " t.id = ".$filter_form['invite_id']." ";
                    $previousConditionPresent = true;
                }
                $criteria->addCondition($condition);
            }

            // exclude_invites_from_me_to_me {
            if (false === isset($filter_form['exclude_invites_from_ne_to_me'])) {
                $filter_form['exclude_invites_from_ne_to_me'] = true;
            } else {
                if ($isReloadRequest) {
                    if (null !== Yii::app()->request->getParam('exclude_invites_from_ne_to_me')) {
                        $filter_form['exclude_invites_from_ne_to_me'] = true;
                    } else {
                        $filter_form['exclude_invites_from_ne_to_me'] = false;
                    }
                }
            }

            if ($filter_form['exclude_invites_from_ne_to_me']) {
                if (false === $previousConditionPresent) {
                    $previousConditionPresent = true;
                } else {
                    $condition .= " AND ";
                }
                $condition .= " receiver_id != owner_id ";
            }
            // exclude_invites_from_me_to_me }

            // exclude developersEmails {
            if (false === isset($filter_form['exclude_developers_emails'])) {
                $filter_form['exclude_developers_emails'] = true;
            } else {
                if ($isReloadRequest) {
                    if (null !== Yii::app()->request->getParam('exclude_developers_emails')) {
                        $filter_form['exclude_developers_emails'] = true;
                    } else {
                        $filter_form['exclude_developers_emails'] = false;
                    }
                }
            }

            if ($filter_form['exclude_developers_emails']) {
                if ($previousConditionPresent) {
                    $condition .= " AND";
                } else {
                    $previousConditionPresent = true;
                }
                $condition .= " email NOT LIKE '%gty1991%' ".
                    " AND email NOT LIKE '%@skiliks.com' ".
                    " AND email NOT LIKE '%@rmqkr.net' ".
                    " AND sent_time > '2013-06-01 00:00:00' ".
                    " AND email NOT IN (".implode(',', $this->developersEmails).") ";
            }
            // exclude developersEmails }

            // filter for statuses {
            $statusesInCriteria = '';
            if (false === isset($filter_form['invite_statuses'])) {
                $filter_form['invite_statuses'] = [
                    Invite::STATUS_PENDING     => true,
                    Invite::STATUS_ACCEPTED    => true,
                    Invite::STATUS_IN_PROGRESS => true,
                    Invite::STATUS_COMPLETED   => true,
                    Invite::STATUS_EXPIRED     => false,
                    Invite::STATUS_DECLINED    => false,
                    Invite::STATUS_DELETED     => false,
                ];
            }

            $newStatuses = Yii::app()->request->getParam('invite_statuses', []);

            if ($isReloadRequest) {
                foreach ($filter_form['invite_statuses'] as $key => $value) {
                    if (isset($newStatuses[$key])) {
                        $filter_form['invite_statuses'][$key] = true;
                        // add status to IN list {
                        // add comma after each not first status in condition:
                        if ('' !== $statusesInCriteria) { $statusesInCriteria .= ', '; }
                        $statusesInCriteria .= sprintf("'%s'", $key);
                        // add status to IN list }
                    } else {
                        $filter_form['invite_statuses'][$key] = false;
                    }
                }
            } else {
                foreach ($filter_form['invite_statuses'] as $key => $value) {
                    if ($value) {
                        if ('' !== $statusesInCriteria) { $statusesInCriteria .= ', '; }
                        $statusesInCriteria .= sprintf("'%s'", $key);
                    }
                }
            }

            if ($previousConditionPresent) {
                $condition .= " AND";
            } else {
                $previousConditionPresent = true;
            }

            if ('' == $statusesInCriteria) {
                $condition .= ' status IS NULL '; // ничего не выбрано из статусов приглашения
            } else {
                $condition .= ' status IN ('.$statusesInCriteria.') ';
            }
            // filter for statuses }

            $criteria->addCondition($condition);
        }

        // update session {
        Yii::app()->session['admin_filter_form'] = $filter_form;
        // update session }

        return [
            "condition" => $condition,
            "criteria"  => $criteria,
            "filters"   => $filter_form
        ];
    }

    public function getCriteriaSimulation() {
        $clear_form = Yii::app()->request->getParam('clear_form', null);
        $criteria = new CDbCriteria;
        $condition = false;

        $isReloadRequest = -1 < strpos(Yii::app()->request->urlReferrer, '/admin_area/simulations');

        // checking if clear form is not null
        if(null !== $clear_form && $clear_form == "admin_simulation_filter_form") {
            $filter_form['scenario'] = [
                Scenario::TYPE_LITE => true,
                Scenario::TYPE_FULL => true,
            ];
        } else {

            // setting up parameters
            $filter_form = Yii::app()->session['admin_simulation_filter_form'];
            $condition = '';

            $emailForFiltration = Yii::app()->request->getParam('email-for-filtration');
            $simulation_id = Yii::app()->request->getParam('simulation_id', null);
            $exceptDevelopersFiltration = (bool)trim(Yii::app()->request->getParam('except-developers', true));


            // remaking email form
            if (null != $emailForFiltration) {
                $filter_form['filter_email'] = $emailForFiltration;
            }
            else $filter_form['filter_email'] = "";

            if (null != $simulation_id) {
                $filter_form['simulation_id'] = $simulation_id;
            }
            else $filter_form['simulation_id'] = "";

            Yii::app()->session['admin_simulation_filter_form'] = $filter_form;

            // checking if filters are not empty
            $previousConditionPresent = false;
            if(null != $filter_form && !empty($filter_form)) {

                    // setting all filters

                if(isset($filter_form['filter_email']) && $filter_form['filter_email'] != "") {
                    $criteria->join = ' LEFT JOIN user AS user ON t.user_id = user.id LEFT JOIN profile AS profile ON user.id = profile.user_id';
                    // for page results
                    $condition = " profile.email LIKE '%".$filter_form['filter_email']."%' ";
                    $previousConditionPresent = true;
                }
                if(isset($filter_form['simulation_id']) && $filter_form['simulation_id'] != "") {
                    if($condition !== "") {
                        $condition .= " AND ";
                    }
                    $condition .= " t.id = ".$filter_form['simulation_id']." ";
                    $previousConditionPresent = true;
                }
                $criteria->addCondition($condition);
            } else {
                $criteria->join = ' LEFT JOIN user AS user ON t.user_id = user.id LEFT JOIN profile AS profile ON user.id = profile.user_id';
            }

            // exclude_invites_from_me_to_me {
            if (false === isset($filter_form['show_simulation_with_end_time'])) {
                $filter_form['show_simulation_with_end_time'] = true;
            } else {
                if ($isReloadRequest) {
                    if (null !== Yii::app()->request->getParam('show_simulation_with_end_time')) {
                        $filter_form['show_simulation_with_end_time'] = true;
                    } else {
                        $filter_form['show_simulation_with_end_time'] = false;
                    }
                }
            }

            if ($filter_form['show_simulation_with_end_time']) {
                if (false === $previousConditionPresent) {
                    $previousConditionPresent = true;
                }
                $condition .= " end IS NOT NULL ";
            }
            // exclude_invites_from_me_to_me }

            // exclude developersEmails {
            if (false === isset($filter_form['exclude_developers_emails'])) {
                $filter_form['exclude_developers_emails'] = true;
            } else {
                if ($isReloadRequest) {
                    if (null !== Yii::app()->request->getParam('exclude_developers_emails')) {
                        $filter_form['exclude_developers_emails'] = true;
                    } else {
                        $filter_form['exclude_developers_emails'] = false;
                    }
                }
            }

            if ($filter_form['exclude_developers_emails']) {
                if ($previousConditionPresent) {
                    $condition .= " AND";
                } else {
                    $previousConditionPresent = true;
                }
                $condition = " profile.email NOT LIKE '%gty1991%' ".
                    " AND profile.email NOT LIKE '%@skiliks.com' ".
                    " AND profile.email NOT LIKE '%@rmqkr.net' ".
                    " AND t.start > '2013-06-01 00:00:00' ".
                    " AND profile.email NOT IN (".implode(',', $this->developersEmails).") ";
            }
            // exclude developersEmails }

            // filter for statuses {
            $scenarioInCriteria = '';
            if (false === isset($filter_form['scenario'])) {
                $filter_form['scenario'] = [
                    Scenario::TYPE_LITE => true,
                    Scenario::TYPE_FULL => true,
                ];
            }

            $newStatuses = Yii::app()->request->getParam('scenario', []);

            $scenarios = [
                Scenario::TYPE_FULL => Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]),
                Scenario::TYPE_LITE => Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]),
            ];

            if ($isReloadRequest) {
                foreach ($filter_form['scenario'] as $key => $value) {
                    if (isset($newStatuses[$key])) {
                        $filter_form['scenario'][$key] = true;
                        // add status to IN list {
                        // add comma after each not first status in condition:
                        if ('' !== $scenarioInCriteria) { $scenarioInCriteria .= ', '; }
                        $scenarioInCriteria .= $scenarios[$key]->id;
                        // add status to IN list }
                    } else {
                        $filter_form['scenario'][$key] = false;
                    }
                }
            } else {
                foreach ($filter_form['scenario'] as $key => $value) {
                    if ($value) {
                        if ('' !== $scenarioInCriteria) { $scenarioInCriteria .= ', '; }
                        $scenarioInCriteria .= $scenarios[$key]->id;
                    }
                }
            }

            if ($previousConditionPresent) {
                $condition .= " AND";
            } else {
                $previousConditionPresent = true;
            }

            if ('' == $scenarioInCriteria) {
                $condition .= ' scenario_id IS NULL '; // ничего не выбрано из статусов приглашения
            } else {
                $condition .= ' scenario_id IN ('.$scenarioInCriteria.') ';
            }
            // filter for statuses }
        }

        Yii::app()->session['admin_simulation_filter_form'] = $filter_form;

        return [
            "condition" => $condition,
            "criteria"  => $criteria,
            "filters"   => $filter_form
        ];
    }

    public function actionInvitesSave() {

        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

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

        if (null === $scData) {
            return 'Файл пуст или ошибка конвертации файла.';
        }

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

        if(isset($invite->simulation)) {
            $simulation = $invite->simulation;
        }
        else {
            $simulation = null;
        }


        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/invite_site_logs_table', [
            'logInvite'     => $logInvite,
            'logSimulation' => $logSimulation,
            'simulation'    => $simulation,
        ]);
    }

    /**
     *
     */
    public function actionSimSiteLogs() {
        $simId = Yii::app()->request->getParam('sim_id', null);
        $logSimulation = LogSimulation::model()->findAllByAttributes(['sim_id' => $simId]);
        $simulation = Simulation::model()->findByPk($simId);
        $this->pageTitle = sprintf('Админка: Лог действий с симуляцией %s на сайте', $simId);
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/simulation_site_logs_table', [
            'logSimulation' => $logSimulation,
            'simulation'    => $simulation
        ]);
    }

    public function actionSimulationSetEmergency($simId)
    {
        /** @var Simulation $simulation */
        $simulation = Simulation::model()->findByPk($simId);
        $simulation->is_emergency_panel_allowed = !$simulation->is_emergency_panel_allowed;
        $simulation->save();

        SimulationService::logAboutSim($simulation, 'Simulation emergency allowed set to ' . $simulation->is_emergency_panel_allowed);
        $this->redirect('/admin_area/simulations');
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

        $condition = null;

        $allFilters = $this->getCriteriaSimulation();

        $criteria = $allFilters['criteria'];

        $totalItems = Simulation::model()->count($criteria);
        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/Simulations';
        // pager }

        $simulations = Simulation::model()
            ->with('user', 'user.profile')
            ->findAll([
                'condition' => (isset($allFilters['condition']) && $allFilters['condition']) ? $allFilters['condition'] : '',
                'order' => 't.id DESC',
                'offset' => ($page-1) * $this->itemsOnPage,
                'limit'  => $this->itemsOnPage
            ]);


        $this->render('/admin_area/pages/simulations_table', [
            'simulations' => $simulations,
            'invites'     => $invites,
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage,
            'formFilters' => $allFilters['filters'],
            'emailForFiltration' => isset($allFilters['filters']['filter_email']) ? $allFilters['filters']['filter_email'] : "",
            'simulation_id' => isset($allFilters['filters']['simulation_id']) ? $allFilters['filters']['simulation_id'] : "",
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
                "offset" => ($page-1)*$this->itemsOnPage,
                "order" => 'id DESC',
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
            Yii::app()->user->setFlash('error', 'Такого пользователя для сайта не существеут.');
            $this->redirect('/admin_area/users');
        }

        // update password {
        $newPassword = Yii::app()->request->getParam('new_password');

        if (null !== $newPassword) {
            if ($siteUser->setPassword($newPassword, YumEncrypt::generateSalt())) {
                Yii::app()->user->setFlash('success', 'Пароль обновлён.');
            } else {
                Yii::app()->user->setFlash('error', 'Пароль не обновлён.');
            }
        }
        // update password }

        $this->pageTitle = 'Админка: смена пароля для '.$siteUser->profile->firstname.' '.$siteUser->profile->lastname;
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/change_site_user_password', [
            'siteUser' => $siteUser,
        ]);
    }

    /**
     * @param $userId
     */
    public function actionSimulationRequests($simId)
    {
        $simulation = Simulation::model()->findByPk($simId);

        if (null === $simulation) {
            Yii::app()->user->setFlash('error', 'Такой симцляции не существеут.');
            $this->redirect('/admin_area/users');
        }

        $simulationLogs = LogServerRequest::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        $this->pageTitle = 'Админка: логи запросов для симуляции '.$simulation->id;
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/simulation_requests', [
            'simulation'     => $simulation,
            'simulationLogs' => $simulationLogs,
        ]);
    }

    public function actionStatistics()
    {
        $this->pageTitle = 'Админка: Движение проглашений в корпоративном аккаунте # ';
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/statistics', []);
    }

    public function actionTestAuth()
    {
        $this->layout = false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://ci.dev.skiliks.com' . $_GET['params']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic aW5kaWNhdG9yOmluZGljYXRvcg==']);
        curl_exec($ch);
    }

    public function actionStatisticFreeDiskSpace(){
        $this->layout = false;
        $bytes = disk_free_space($_SERVER['DOCUMENT_ROOT']);
        $free = round($bytes/1024/1024/1024);
        if($free <= 5) {
            $res['status'] = 'failure';
        } else {
            $res['status'] = 'success';
        }
        $res['data'] = " {$free} Gb";
        echo json_encode($res);
    }

    public function actionStatisticOrderCount()
    {
        $this->layout = false;
        $all = Invoice::model()->count();
        $today = (int)Invoice::model()->count('created_at > CURDATE()');
        if($today !== 0) {
            $res['status'] = 'failure';
        } else {
            $res['status'] = 'success';
        }
        $res['data'] = " {$today}/{$all}";
        echo json_encode($res);
    }

    public function actionStatisticFeedbackCount()
    {
        $all = (int)Feedback::model()->count();
        $today = (int)Feedback::model()->count(" addition >= :addition", ['addition'=>(new DateTime())->format("Y-m-d")]);
        if($today !== 0) {
            $res['status'] = 'failure';
        } else {
            $res['status'] = 'success';
        }
        $res['data'] = " {$today}/{$all}";
        echo json_encode($res);
    }

    public function actionStatisticCrashSimulation()
    {
        $full = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        $lite = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_LITE]);
        $full_crash = (int)Simulation::model()->count(" end is null and start <= :start and scenario_id = :scenario_id", [
            'start'=>date('Y-m-d H:i:s', strtotime('-3 hours')),
            'scenario_id'=>$full->id
        ]);
        $lite_crash = (int)Simulation::model()->count(" end is null and start <= :start and scenario_id = :scenario_id", [
            'start'=>date('Y-m-d H:i:s', strtotime('-1 hours')),
            'scenario_id'=>$lite->id
        ]);
        $total = $full_crash + $lite_crash;
        if($total !== 0) {
            $res['status'] = 'failure';
        } else {
            $res['status'] = 'success';
        }
        $res['data'] = " {$total} ";
        echo json_encode($res);
    }

    /**
     * @param $inviteId
     */
    public function actionInviteSwitchCanBeReloaded($inviteId) {
        $invite = Invite::model()->findByPk($inviteId);

        if (null === $invite) {
            Yii::app()->user->setFlash('success', sprintf(
                'Приглашениe #%s не найдено.',
                $inviteId
            ));
            $this->redirect('/admin_area/invites');
        }

        $invite->can_be_reloaded = ($invite->can_be_reloaded) ? 0 : 1;
        $invite->save(false);

        Yii::app()->user->setFlash('success', sprintf(
            'По приглашению #%s для %s %s %s, теперь %s заново начать симуляцию.',
            $invite->id,
            $invite->firstname,
            $invite->lastname,
            $invite->email,
            ($invite->can_be_reloaded) ? 'можно': 'нельзя'
        ));

        $this->redirect('/admin_area/invites');
    }

    public function actionSimulationFixEndTime($simId) {
        $simulation = Simulation::model()->findByPk($simId);

        if (null !== $simulation && null === $simulation->end && null!== $simulation->start) {
            $simulation->end = '0001-01-01 01:01:01';
            $simulation->save(false);
        }

        $this->redirect('/admin_area/simulations');
    }

    public function actionFeedBacksList()
    {
        $this->pageTitle = 'Админка: Список отзывов';
        $this->layout = '//admin_area/layouts/admin_main';

        $this->render('/admin_area/pages/feedbacks_table', [
            'feedbacks' => Feedback::model()->findAll([
                "order" => 'id DESC',
            ]),
        ]);
    }

    public function actionSubscribersList()
    {
        $emails = Yii::app()->db->createCommand()
            ->select( 'id, email' )
            ->from( 'emails_sub' )
            ->queryAll();

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/subscribers_table', [
            'subscribersEmails' => $emails,
        ]);
    }

    public function actionUserDetails($userId)
    {
        $user = YumUser::model()->findByPk($userId);

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/user_details', [
            'user' => $user,
        ]);
    }

    public function actionUserSetTariff($userId, $label)
    {
        $user = YumUser::model()->findByPk($userId);
        if (null === $user ) {
            Yii::app()->user->setFlash('error', sprintf(
                'Не найден пользователь с номером "%s".',
                $userId
            ));
            $this->redirect('/admin_area/dashboard');
        }

        if (false == $user->isCorporate()) {
            Yii::app()->user->setFlash('error', sprintf(
                'Не найден пользователь "%s %s" не является корпоративным пользователем.',
                $user->profile->firstname,
                $user->profile->lastname,
                $userId
            ));
            $this->redirect('/admin_area/user/'.$userId.'/details');
        }

        $initValue = $user->getAccount()->invites_limit;
        $tariff = Tariff::model()->findByAttributes(['slug' => $label]);

        if (null == $tariff) {
            UserService::logCorporateInviteMovementAdd(
                'Cheats: actionChooseTariff, NULL tariff',
                $user->getAccount(),
                $initValue
            );
            Yii::app()->user->setFlash('error', sprintf(
                'Не найден тариф "%s".',
                $label
            ));
            $this->redirect('/admin_area/dashboard');
        }

        // set Tariff {
        $user->getAccount()->tariff_id = $tariff->id;
        $user->getAccount()->tariff_activated_at = date('Y-m-d H:i:s');
        $user->getAccount()->tariff_expired_at = date('Y-').(date('m')+1).date('-d H:i:s');
        $user->getAccount()->invites_limit = $tariff->simulations_amount;
        $user->getAccount()->save();
        // set Tariff }

        UserService::logCorporateInviteMovementAdd(
            'Cheats: actionChooseTariff, tariff not null',
            $user->getAccount(),
            $initValue
        );

        Yii::app()->user->setFlash('success', sprintf(
            'Активирован тарифный план "%s" для "%s %s".',
            ucfirst($label),
            $user->profile->firstname,
            $user->profile->lastname
        ));

        $this->redirect('/admin_area/user/'.$userId.'/details');
    }

    public function actionUserAddRemoveInvitations($userId, $value)
    {
        $user = YumUser::model()->findByPk($userId);
        if (null === $user ) {
            Yii::app()->user->setFlash('error', sprintf(
                'Не найден пользователь с номером "%s".',
                $userId
            ));
            $this->redirect('/admin_area/dashboard');
        }

        // set invites_limit {
        $initValue = $user->getAccount()->invites_limit;

        $user->getAccount()->invites_limit += $value;
        if ($user->getAccount()->invites_limit < 0) {
            $user->getAccount()->invites_limit = 0;
        }
        $user->getAccount()->save();
        // set invites_limit }

        UserService::logCorporateInviteMovementAdd(
            'Cheats: action set invites_limit, to'.$user->getAccount()->invites_limit,
            $user->getAccount(),
            $initValue
        );

        Yii::app()->user->setFlash('success', sprintf(
            'Количество доступных симуляций для "%s %s" установнено %s.',
            $user->profile->firstname,
            $user->profile->lastname,
            $user->getAccount()->invites_limit
        ));

        $this->redirect('/admin_area/user/'.$userId.'/details');
    }

    public function actionImportsList()
    {
        $scenarios = Scenario::model()->findAll();

        $logs = LogImport::model()->findAll(['order' => 'started_at DESC', 'limit' => 10]);

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/imports_table', [
            'scenarios' => $scenarios,
            'logs'      => $logs,
        ]);
    }

    public function actionStartImport($slug, $logImportId)
    {
        $scenario = Scenario::model()->findByAttributes(['slug' => $slug]);

        if (null === $scenario) {
            Yii::app()->user->setFlash('error', sprintf(
                'Не найден сценарий "%s".',
                $slug
            ));
            $this->redirect('/admin_area/import');
        }

        $log = LogImport::model()->findByPk($logImportId);

        if (null === $scenario) {
            Yii::app()->user->setFlash('error', sprintf(
                'Не найден лог импорта "%s".',
                $logImportId
            ));
            $this->redirect('/admin_area/import');
        }

        $log->scenario = $scenario;
        $log->scenario_id = $scenario->id;
        $log->save();

        $import = new ImportGameDataService($scenario->slug, 'db', $log);
        $import->importAll();

        $log->finished_at = date('Y-m-d H:i:s');
        $log->save();
    }

    public function actionGetImportLog($id)
    {
        $log = LogImport::model()->findByPk($id);

        if (null !== $log) {
            $text = $log->text;
            if (null === $text) {
                $text = file_get_contents(__DIR__.'/../../logs/'.$id.'-import.log');
            }
            echo json_encode([
                'text'        => $text,
                'finish_time' => $log->finished_at,
            ]);
        } elseif (0 == $id) {
            $log = new LogImport();
            $log->user = Yii::app()->user->data();
            $log->user_id = Yii::app()->user->data()->id;
            $log->started_at  = date('Y-m-d H:i:s');
            $log->save();
            echo json_encode([
                'log_id'=> $log->id,
            ]);
        }

        Yii::app()->end();
    }

    public function actionIncreaseInvites()
    {
        $user = Yii::app()->user->data();

        if (false == $user->isCorporate()) {
            $this->redirect('/admin_area/dashboard');
        }

        $initValue = $user->getAccount()->invites_limit;

        $user->getAccount()->invites_limit += 10;
        $user->getAccount()->save();

        UserService::logCorporateInviteMovementAdd(
            'Cheats: actionIncreaseInvites',
            $user->getAccount(),
            $initValue
        );

        Yii::app()->user->setFlash('success', "Вам добавлено 10 приглашений!");

        $this->redirect('/admin_area/dashboard');
    }
}