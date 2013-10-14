<?php

class AdminPagesController extends SiteBaseController {

    public $itemsOnPage = 20;

    public $user;

    public function beforeAction($action) {

        $public = ['Login'];
        $user = Yii::app()->user->data();
        $this->user = $user;
        if(in_array($action->id, $public)){
            return true;
        }elseif(!$user->isAuth()){
            $this->redirect('/admin_area/login');
        }elseif(!$user->isAdmin()){
            $this->redirect('/dashboard');
        }
        return true;
    }

    public function actionIndex() {
        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/dashboard', ['user'=>$this->user]);
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
        $page = Yii::app()->request->getParam('invites-filter-page');

        if (null === $page) {
            $page = 1;
        }

        $this->itemsOnPage = 100;

        $allFilters = $this->getCriteriaInvites();

        // creating criteria for search
        $criteria = $allFilters['criteria'];
        $criteria->condition = $allFilters['condition'];
        $criteria->order     = "updated_at desc";

        $totalItems = Invite::model()->count($criteria);

        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/Invites';
        // pager }

        //$models = Invite::model()->findAll([

        $models = Invite::model()->findAll($criteria);

//        if (count($models) < $this->itemsOnPage) {
//            $page = 1; // если результатов фильтрации мало
//
//            $models = Invite::model()->findAll($criteria);
//        }

        // getting scenarios type
        $scenarioCriteria = new CDbCriteria();
        $scenarioCriteria->distinct = true;
        $scenarios = Scenario::model()->findAll($scenarioCriteria);

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/invites', [
            'models'                     => $models,
            'page'                       => $page,
            'pager'                      => $pager,
            'totalItems'                 => $totalItems,
            'itemsOnPage'                => $this->itemsOnPage,
            'formFilters'                => $allFilters['filters'],
            'receiverEmailForFiltration' => isset($allFilters['filters']['filter_email']) ? $allFilters['filters']['filter_email'] : "",
            'ownerEmailForFiltration'    => isset($allFilters['filters']['owner_email']) ? $allFilters['filters']['owner_email'] : "",
            'invite_id'                  => isset($allFilters['filters']['invite_id']) ? $allFilters['filters']['invite_id'] : "",
            'scenario_id'                => isset($allFilters['filters']['filter_scenario_id']) ? $allFilters['filters']['filter_scenario_id'] : "",
            'scenarios'                  => $scenarios
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
            $ownerEmailForFiltration = trim(Yii::app()->request->getParam('owner_email_for_filtration', null));
            $invite_id = trim(Yii::app()->request->getParam('invite_id', null));
            $exceptDevelopersFiltration = (bool)trim(Yii::app()->request->getParam('except-developers', true));
            $simulationScenario = Yii::app()->request->getParam('filter_scenario_id', true);

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
                if (null !== $ownerEmailForFiltration) {
                    $filter_form['owner_email'] = $ownerEmailForFiltration;
                }
                else {
                    $filter_form['owner_email'] = "";
                }
            }

            if ($isReloadRequest) {
                if (null !== $exceptDevelopersFiltration) {
                    $filter_form['exceptDevelopersFiltration'] = $exceptDevelopersFiltration;
                }
                else {
                    $filter_form['exceptDevelopersFiltration'] = "";
                }
            }

            if ($isReloadRequest) {
                if (null !== $simulationScenario) {
                    $filter_form['filter_scenario_id'] = $simulationScenario;
                }
                else {
                    $filter_form['filter_scenario_id'] = "";
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
                    $condition .= " t.email LIKE '%".$filter_form['filter_email']."%' ";
                    $previousConditionPresent = true;
                }
                if(isset($filter_form['invite_id']) && $filter_form['invite_id'] && $filter_form['invite_id'] != "" ) {
                    if($condition !== "") {
                        $condition .= " AND ";
                    }
                    $condition .= " t.id = ".$filter_form['invite_id']." ";
                    $previousConditionPresent = true;
                }

                if(isset(   $filter_form['owner_email'])
                         && $filter_form['owner_email']
                         && $filter_form['owner_email'] != ""
                ) {
                    if($condition !== "") {
                        $condition .= " AND ";
                    }
                    $criteria->select = 't.*, owner.email as owner_email';
                    $criteria->join = ' LEFT JOIN profile AS owner ON owner.user_id = t.owner_id ';
                    $condition .= " owner.email LIKE '%".$filter_form['owner_email']."%' ";
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

            if (isset($filter_form["filter_scenario_id"]) && $filter_form["filter_scenario_id"] != "") {
                if (false === $previousConditionPresent) {
                    $previousConditionPresent = true;
                } else {
                    $condition .= " AND ";
                }
                $condition .= ' scenario_id = '.$filter_form["filter_scenario_id"] ;
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
                    " AND email NOT IN (".implode(',', UserService::$developersEmails).") ";
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
                    " AND profile.email NOT IN (".implode(',', UserService::$developersEmails).") ";
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

        $request_uri = $_SERVER['REQUEST_URI'];

        $disableFilters = Yii::app()->request->getParam("disable_filters", null);
        // adding session
        $session = new CHttpSession();

        // taking up address to

        if( null !== $disableFilters) {
            $address = '/admin_area/orders';
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
        $filterCash = Yii::app()->request->getParam('cash', null);
        $filterRobokassa = Yii::app()->request->getParam('robokassa', null);

        if($filterCash !== null && $filterRobokassa === null) {
            $criteria->compare("payment_system", 'cash');
        }
        elseif($filterCash === null && $filterRobokassa !== null) {
            $criteria->compare("t.payment_system", 'robokassa');
        }
        // if both are not null we taking everything


        // applying done / not done filters
        $done = Yii::app()->request->getParam('done', null);
        $notDone = Yii::app()->request->getParam('notDone', null);

        if($done !== null && $notDone === null) {
            $criteria->addCondition("t.paid_at IS NOT NULL");
        }
        elseif($done === null && $notDone !== null) {
            $criteria->addCondition("t.paid_at IS NULL");
        }
        // if both are not null we taking everything

        // setting the form to get it in the view

        // checking if submit button wasn't pushed
        $formSended = Yii::app()->request->getParam('form-send', null);

        if($formSended !== null) {
            $appliedFilters = ["email"     =>$filterEmail,
                               "robokassa" =>$filterRobokassa,
                               "cash"      =>$filterCash,
                               "done"      =>$done,
                               "notDone"   =>$notDone
                              ];
        }
        else {
            // generationg the all filters to be checked
            $appliedFilters = ["email"     => null,
                               "robokassa" => "set",
                               "cash"      => "set",
                               "done"      => "set",
                               "notDone"   => "set"
            ];
        }


        // counting objects to make the pagination
        $totalItems = count(Invoice::model()->findAll($criteria));

        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/Orders';
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

    public function actionReferralsList() {
        $dataProvider = UserReferral::model()->searchReferrals();
        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/referrals_list', ['dataProvider' => $dataProvider]);
    }

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
                    "Принята оплата по счёт-фактуре номер %s, на тарифный план %s. Количество доступных симуляций установлено в %s из них за рефераллов %s. Админ %s.",
                    $invoice->id, $invoice->tariff->label, $invoice->tariff->simulations_amount, $invoice->user->getAccount()->referrals_invite_limit, $admin->profile->email
                ),  $invoice->user->getAccount(), $initValue);

            echo json_encode(["return" => true, "paidAt" => $invoice->paid_at]);
        }
    }

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

    public function actionCommentInvoice() {
        $invoiceId = Yii::app()->request->getParam('invoice_id');
        $criteria = new CDbCriteria();
        $criteria->compare('id', $invoiceId);

        $invoice = Invoice::model()->find($criteria);

        if($invoice !== null) {

            $invoice->comment = (Yii::app()->request->getParam('invoice_comment'));
            $invoice->save();
            $user = Yii::app()->user->data();
            $invoice_log = new LogPayments();
            $invoice_log->log($invoice, $user->profile->email . " изменил комментарий: \r\n".$invoice->comment);
            echo json_encode(["return" => true]);
        }
    }

    public function actionGetInvoiceLog() {
        $invoiceId = Yii::app()->request->getParam('invoice_id');
        $criteria = new CDbCriteria();
        $criteria->compare('invoice_id', $invoiceId);

        $logs = LogPayments::model()->findAll($criteria);
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
            $invite_status = $invite->status;
            $invite->status = $status;
            if(false === $invite->save(false)){
                throw new Exception("Not saved");
            }
            InviteService::logAboutInviteStatus($invite, 'Админ '.$this->user->profile->email.' изменил статус с'.Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($invite->status));
        } else {
            throw new Exception("Status not found");
        }

        $this->redirect("/admin_area/invites");
    }

    public function actionInviteCalculateTheEstimate() {

        $simId = Yii::app()->request->getParam('sim_id', null);
        $email = strtolower(str_replace(' ', '+', Yii::app()->request->getParam('email', null)));
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
                "offset" => ($page-1)*$this->itemsOnPage,
                "order"  => ' user_id DESC ',
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
        $logs = LogAccountInvite::model()->findAll([
            'condition' => 'user_id = :id ',
            'params' => [
                'id' => Yii::app()->request->getParam('id', null)
            ],
            'order' => 'date DESC',
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

    public function actionStatisticMail(){
        $pending = (int)EmailQueue::model()->count("status = :status", ['status'=>EmailQueue::STATUS_PENDING]);
        $in_progress = EmailQueue::model()->count("status = :status", ['status'=>EmailQueue::STATUS_IN_PROGRESS]);
        $sended = EmailQueue::model()->count("status = :status", ['status'=>EmailQueue::STATUS_SENDED]);
        if($pending !== 0) {
            $res['status'] = 'failure';
        } else {
            $res['status'] = 'success';
        }
        $res['data'] = $pending.'/'.$in_progress.'/'.$sended;
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

        if($user->isCorporate()) {
            $isSwitchShowReferralInfoPopup = Yii::app()->request->getParam("switchReferralInfoPopup", null);
            if($isSwitchShowReferralInfoPopup !== null) {
                $user->account_corporate->is_display_referrals_popup = !$user->account_corporate->is_display_referrals_popup;
                $user->account_corporate->save();
            }

            $isSwitchTariffExpiredPopup = Yii::app()->request->getParam("switchTariffExpiredPopup", null);
            if($isSwitchTariffExpiredPopup !== null) {
                $user->account_corporate->is_display_tariff_expire_pop_up = !$user->account_corporate->is_display_tariff_expire_pop_up;
                $user->account_corporate->save();
            }
        }

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

        $initValue = $user->getAccount()->getTotalAvailableInvitesLimit();
        $tariff = Tariff::model()->findByAttributes(['slug' => $label]);

        if (null == $tariff) {
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

        $admin = Yii::app()->user->data();

        $user->getAccount()->referrals_invite_limit;

        UserService::logCorporateInviteMovementAdd(
            sprintf('Тарифный план для %s сменён на %s из админ области. Количество доступных симуляций установлено в %s из них за рефераллов %s. Админ %s.',
                $user->profile->email, $tariff->label, $user->getAccount()->invites_limit,
                $user->getAccount()->referrals_invite_limit, $admin->profile->email
            ),
            $user->getAccount(),
            $user->getAccount()->getTotalAvailableInvitesLimit()
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
        $admin = Yii::app()->user->data();
        $user = YumUser::model()->findByPk($userId);
        if (null === $user ) {
            Yii::app()->user->setFlash('error', sprintf(
                'Не найден пользователь с номером "%s".',
                $userId
            ));
            $this->redirect('/admin_area/dashboard');
        }

        // set invites_limit {
        $initValue = $user->getAccount()->getTotalAvailableInvitesLimit();
        $account = $user->getAccount();

        $user->getAccount()->invites_limit += $value;
        if ($user->getAccount()->invites_limit < 0) {
            $user->getAccount()->invites_limit = 0;
        }
        $user->getAccount()->save();
        // set invites_limit }

        UserService::logCorporateInviteMovementAdd(
           sprintf('Количество доступных симуляций установлено в %s в админ области, из них за рефераллов %s. '.
           ' Админ %s (емейл текущего авторизованного в админке пользователя).', $account->invites_limit, $account->referrals_invite_limit, $admin->profile->email),
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


    public function actionRegistrationList() {
        // getting registration by day
        $userCounter = new countRegisteredUsers();
        $userCounter->getAllUserForDays();
        $userCounter->getNonActiveUsersForDays();

        $dayDate = new DateTime();

        // TODO registration

        $registrationsByDay = [];
        for($i = 0; $i<30; $i++) {
            $day = date_format($dayDate, 'Y-m-d');
            $registrationsByDay[$day]['period'] = $day;
            $registrationsByDay[$day]['totalRegistrations'] = isset($userCounter->totalRegistrations[$day]) ? $userCounter->totalRegistrations[$day] : 0;
            $registrationsByDay[$day]['totalPersonals'] = isset($userCounter->totalPersonals[$day]) ? $userCounter->totalPersonals[$day] : 0;
            $registrationsByDay[$day]['totalCorporate'] = isset($userCounter->totalCorporate[$day]) ? $userCounter->totalCorporate[$day] : 0;
            $registrationsByDay[$day]['totalNonActivePersonals'] = isset($userCounter->totalNonActivePersonals[$day]) ? $userCounter->totalNonActivePersonals[$day] : 0;
            $registrationsByDay[$day]['totalNonActiveCorporate'] = isset($userCounter->totalNonActiveCorporate[$day]) ? $userCounter->totalNonActiveCorporate[$day] : 0;

            $dateInterval = new DateInterval('P1D');
            $dateInterval->invert = 1;
            $dayDate->add($dateInterval);
        }

        // getting registration by month
        $userCounter = new countRegisteredUsers();
        $userCounter->getAllUserForDays();
        $userCounter->getNonActiveUsersForDays();

        $dayDate = new DateTime();

        $registrationsByMonth = [];
        for($i = 0; $i<12; $i++) {
            $day = date_format($dayDate, 'F');
            $registrationsMonth[$day]['period'] = $day;
            $registrationsMonth[$day]['totalRegistrations'] = isset($userCounter->totalRegistrations[$day]) ? $userCounter->totalRegistrations[$day] : 0;
            $registrationsMonth[$day]['totalPersonals'] = isset($userCounter->totalPersonals[$day]) ? $userCounter->totalPersonals[$day] : 0;
            $registrationsMonth[$day]['totalCorporate'] = isset($userCounter->totalCorporate[$day]) ? $userCounter->totalCorporate[$day] : 0;
            $registrationsMonth[$day]['totalNonActivePersonals'] = isset($userCounter->totalNonActivePersonals[$day]) ? $userCounter->totalNonActivePersonals[$day] : 0;
            $registrationsMonth[$day]['totalNonActiveCorporate'] = isset($userCounter->totalNonActiveCorporate[$day]) ? $userCounter->totalNonActiveCorporate[$day] : 0;

            $dateInterval = new DateInterval('P1M');
            $dateInterval->invert = 1;
            $dayDate->add($dateInterval);
        }

        // getting registration by year
        $userCounter = new countRegisteredUsers();
        $userCounter->getAllUserForYears();
        $userCounter->getNonActiveUserForYears();

        $dayDate = new DateTime();

        $registrationsByYear = [];
        $day = date_format($dayDate, 'Y');
        $registrationsByYear[$day]['period'] = $day;
        $registrationsByYear[$day]['totalRegistrations'] = isset($userCounter->totalRegistrations[$day]) ? $userCounter->totalRegistrations[$day] : 0;
        $registrationsByYear[$day]['totalPersonals'] = isset($userCounter->totalPersonals[$day]) ? $userCounter->totalPersonals[$day] : 0;
        $registrationsByYear[$day]['totalCorporate'] = isset($userCounter->totalCorporate[$day]) ? $userCounter->totalCorporate[$day] : 0;
        $registrationsByYear[$day]['totalNonActivePersonals'] = isset($userCounter->totalNonActivePersonals[$day]) ? $userCounter->totalNonActivePersonals[$day] : 0;
        $registrationsByYear[$day]['totalNonActiveCorporate'] = isset($userCounter->totalNonActiveCorporate[$day]) ? $userCounter->totalNonActiveCorporate[$day] : 0;

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/registrationCounterList',
            [
                'registrationsByDay'     => $registrationsByDay,
                'registrationsByMonth'   => $registrationsMonth,
                'registrationsByYear'    => $registrationsByYear,
            ]
        );
    }





    // формирование отчетов

    public function actionMakeReport($ids = false)
    {
        if($ids) {
            $saves = 0;
            $overwrite = true;
            $ids = explode(",", $ids);
            if(!empty($ids)) {
                $simulations = array();
                foreach($ids as $row) {
                    $simulation = Simulation::model()->findByPk($row);
                    if($simulation !== null) {
                        $simulations[] = $simulation;
                        echo "{$simulation->id}, ";
                    }
                }

                if(!empty($simulations)) {
                    SimulationService::saveLogsAsExcelCombined($simulations);
                }

                echo " {$saves} files stored!\r\n";
            }
        }
    }

    public function actionUserReferrals($userId = false) {
        if($userId) {
            $user = YumUser::model()->findByPk($userId);
            $totalReferrals = UserReferral::model()->countUserReferrals($user->id);
            $this->layout = '/admin_area/layouts/admin_main';
            $dataProvider = UserReferral::model()->searchUserReferrals($user->id);
            $this->render('/admin_area/pages/user_referrals_list', ['totalRefers'=>$totalReferrals, 'user'=>$user,
                    'dataProvider' => $dataProvider]);
        }
    }

    public function actionEmailQueue()
    {
        $formFilters = Yii::app()->session['admin_email_queue_filter_form'];

        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            if (isset($formFilters['page'])) {
                $page = $formFilters['page'];
            } else {
                $page = 1;
                $formFilters['page'] = 1;
            }
        }

        $criteria = new CDbCriteria;

        // applying filters
        // sender_email {
        if (isset($formFilters['sender_email'])) {
            $filterSenderEmail = $formFilters['sender_email'];
        } else {
            $filterSenderEmail = Yii::app()->request->getParam('sender_email', null);
            $formFilters['sender_email'] = $filterSenderEmail;
        }

        if($filterSenderEmail !== null) {
            $filterSenderEmail = trim($filterSenderEmail);
            $criteria->addSearchCondition("t.sender_email", $filterSenderEmail);
        }
        // sender_email }

        // recipients {
        if (isset($formFilters['recipients'])) {
            $filterRecipients = $formFilters['recipients'];
        } else {
            $filterRecipients = Yii::app()->request->getParam('recipients', null);
            $formFilters['recipients'] = $filterRecipients;
        }

        if($filterRecipients !== null) {
            $filterRecipients = trim($filterRecipients);
            $criteria->addSearchCondition("t.recipients", $filterRecipients);
        }
        // recipients }

        // send / not_send {
        if (isset($formFilters['send'])) {
            $filterStatusSend = $formFilters['send'];
        } else {
            $filterStatusSend = Yii::app()->request->getParam('send', null);
            $formFilters['send'] = $filterStatusSend;
        }

        if (isset($formFilters['not_send'])) {
            $filterStatusNotSend = $formFilters['not_send'];
        } else {
            $filterStatusNotSend = Yii::app()->request->getParam('not_send', null);
            $formFilters['not_send'] = $filterStatusNotSend;
        }

        if($filterStatusSend !== null && $filterStatusNotSend == null) {
            // only send
            $criteria->addCondition(' t.sended_at IS NOT NULL ');
        } else if ($filterStatusSend == null && $filterStatusNotSend !== null) {
            // only not send
            $criteria->addCondition(' t.sended_at IS NULL ');
        }
        // send / not_send }

        Yii::app()->session['admin_email_queue_filter_form'] = $formFilters;

        if($filterSenderEmail !== null) {
            $appliedFilters = [
                "sender_email" => $filterSenderEmail,
                "recipients"   => $filterRecipients,
                "send"         => $filterStatusSend,
                "not_send"     => $filterStatusNotSend,
            ];
        }
        else {
            // generation the all filters to be checked
            $appliedFilters = [
                "sender_email" => null,
                "recipients"   => null,
                "send"         => null,
                "not_send"     => null,
            ];
        }

        // counting objects to make the pagination
        $totalItems = count(EmailQueue::model()->findAll($criteria));

        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/email_queue';
        // pager }

        // building criteria
        $criteria->order = "created_at desc" ;
        $criteria->limit = $this->itemsOnPage;
        $criteria->offset = ($page-1)*$this->itemsOnPage;

        $emails = EmailQueue::model()->findAll($criteria);

        $this->layout = '//admin_area/layouts/admin_main';

        $this->render('/admin_area/pages/email_queue', [
            'emails'      => $emails,
            'page'        => $page,
            'pager'       => $pager,
            'totalItems'  => $totalItems,
            'itemsOnPage' => $this->itemsOnPage,
            'filters'     => $appliedFilters
        ]);
    }

    public function actionEmailText($id = null) {
        if(null !== $id) {
            $email = EmailQueue::model()->findByPk($id);

            $this->layout = '/admin_area/layouts/admin_main';
            $this->render('/admin_area/pages/email_text', [
                "email" => $email,
            ]);
        }
    }

    public function actionSimulationsRating()
    {
        $condition = Simulation::model()->getSimulationRealUsersCondition(
            '',
            AssessmentCategory::PERCENTILE
        );

        $assessments = AssessmentOverall::model()->with('sim', 'sim.user', 'sim.user.profile') ->findAll([
            'condition' => $condition,
            'order'     => ' t.value DESC '
        ]);

        $simulations = [];
        foreach ($assessments as $assessment) {
            $simulations[] = $assessment->sim;
        }

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/simulations_rating', [
            "simulations" => $simulations,
        ]);
    }

    public function actionSimulationsRatingCsv()
    {
        $condition = Simulation::model()->getSimulationRealUsersCondition(
            '',
            AssessmentCategory::PERCENTILE
        );

        $assessments = AssessmentOverall::model()->with('sim', 'sim.user', 'sim.user.profile') ->findAll([
            'condition' => $condition,
            'order'     => ' t.value DESC '
        ]);

        $xlsFile =  new \PHPExcel();
        $xlsFile->removeSheetByIndex(0);

        $worksheet = new \PHPExcel_Worksheet($xlsFile, 'Процентили');
        $xlsFile->addSheet($worksheet);

        $worksheet->setCellValueByColumnAndRow(1, 1, "ID инвайта");
        $worksheet->setCellValueByColumnAndRow(2, 1, "Sim. ID");
        $worksheet->setCellValueByColumnAndRow(3, 1, "Email соискателя, игрока");
        $worksheet->setCellValueByColumnAndRow(4, 1, "Время начала симуляции");
        $worksheet->setCellValueByColumnAndRow(5, 1, "Время конца симуляции");
        $worksheet->setCellValueByColumnAndRow(6, 1, "Сценарий: статус");
        $worksheet->setCellValueByColumnAndRow(7, 1, "Оценка звёзды");
        $worksheet->setCellValueByColumnAndRow(8, 1, "Процентиль");

        $i = 3;
        foreach ($assessments as $assessment) {
            $worksheet->setCellValueByColumnAndRow(1, $i, $assessment->sim->invite->id );
            $worksheet->setCellValueByColumnAndRow(2, $i, $assessment->sim->id );
            $worksheet->setCellValueByColumnAndRow(3, $i, $assessment->sim->user->profile->email );
            $worksheet->setCellValueByColumnAndRow(4, $i, $assessment->sim->start );
            $worksheet->setCellValueByColumnAndRow(5, $i, $assessment->sim->end );
            $worksheet->setCellValueByColumnAndRow(6, $i, $assessment->sim->status );
            $worksheet->setCellValueByColumnAndRow(7, $i, $assessment->sim->invite->getOverall() );
            $worksheet->setCellValueByColumnAndRow(8, $i, $assessment->sim->invite->getPercentile() );
            $i++;
        }

        $doc = new \PHPExcel_Writer_Excel2007($xlsFile);
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"percentile.xlsx\"");
        $doc->save('php://output');
    }

    public function actionSendNotice() {
        $user_id = Yii::app()->request->getParam('user_id');
        $user = YumUser::model()->findByPk($user_id);
        /* @var YumUser $user */
        $before_email = $user->profile->email;
        MailHelper::sendNoticeEmail($user);
        $user->refresh();
        echo "Before - ".$before_email.' and After - '.$user->profile->email;
    }

    public function actionUpdateInviteEmail() {
        $user_id = Yii::app()->request->getParam('user_id');
        $invites = Invite::model()->findAll("owner_id = receiver_id and owner_id = {$user_id}");
        /* @var Invite $invite */
        foreach($invites as $invite) {
            MailHelper::updateInviteEmail($invite);
        }
        echo "Done";
    }

    /**
     * Позволяет админам (некоторым админам) заходить на сайт от имени любого аккаунта
     *
     * Зависит от параметров: isBlockGhostLogin, isUseStrictRulesForGhostLogin
     *
     * @param integer $userId
     */
    public function actionGhostLogin($userId)
    {
        // функционал заблокирован совсем?
        if (Yii::app()->params['isBlockGhostLogin']) {
            $this->redirect('/admin_area/users');
        }

        // включено ограничек по кругу лиц, допущенных к функционалу
        if (Yii::app()->params['isUseStrictRulesForGhostLogin'] &&
            false == in_array(Yii::app()->user->data()->profile->email, ['slavka@skiliks.com', 'tony@skiliks.com', 'tatiana@skiliks.com'])) {
            $this->redirect('/admin_area/users');
        }

        // проверка существования пользователя
        $user = YumUser::model()->findByPk($userId);

        if (null === $user) {
            Yii::app()->user->setFlash('error', "Пользователя с ID {$userId} не существует.");
            $this->redirect('/admin_area/users');
        }

        // непосредственно "пере-аутентификация"
        $identity = new YumUserIdentity($user->username, false);

        $identity->authenticate(true);

        Yii::app()->user->login($identity);

        $this->redirect('/dashboard');
    }
}