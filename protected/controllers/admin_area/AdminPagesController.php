<?php

class AdminPagesController extends SiteBaseController {

    public $itemsOnPage = 100;

    public $user;

    public function beforeAction($action) {

        $public = ['Login'];
        $user = Yii::app()->user->data();
        $this->user = $user;
        if(in_array($action->id, $public)){
            parent::beforeAction($action);
            return true;
        }elseif(!$user->isAuth()){
            $this->redirect('/admin_area/login');
        }elseif(!$user->isAdmin()){
            $this->redirect('/dashboard');
        }
        parent::beforeAction($action);
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
        $full_simulations = Simulation::model()->findAll([
            'condition' => " `game_type`.`slug` = 'full' AND `t`.`start` > (NOW() - interval 3 HOUR) ",
            'with'=>array(
                'user',
                'invite',
                'game_type',
            ),
            'order'  => " t.start desc",
        ]);


        $lite_simulations = Simulation::model()->findAll([
            'condition' => " `game_type`.`slug` = 'lite' AND `t`.`start` > (NOW() - interval 35 MINUTE) ",
            'with'=>array(
                'user',
                'invite',
                'game_type',
            ),
            'order'  => " t.start desc",
        ]);


        $tutorial_simulations = Simulation::model()->findAll([
            'condition' => " `game_type`.`slug` = 'tutorial' AND `t`.`start` > (NOW() - interval 15 MINUTE) ",
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

                UserService::addAuthorizationLog($_POST['YumUserLogin']['username'], null, SiteLogAuthorization::SUCCESS_AUTH, $model->user->id, SiteLogAuthorization::ADMIN_AREA);
                $model->user->authenticate($form['password']);
                $this->redirect('/admin_area/dashboard');
            } else {
                UserService::addAuthorizationLog($_POST['YumUserLogin']['username'], $_POST['YumUserLogin']['password'], SiteLogAuthorization::FAIL_AUTH, null, SiteLogAuthorization::ADMIN_AREA);
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
            'is_invite_crashed'          => isset($allFilters['filters']['is_invite_crashed']) ? $allFilters['filters']['is_invite_crashed'] : "",
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
                Invite::STATUS_DECLINED    => false,
                Invite::STATUS_DELETED     => false,
            ];
        } else {
            // setting up parameters
            $filter_form = Yii::app()->session['admin_filter_form'];

            $condition = '';

            $receiverEmailForFiltration = trim(Yii::app()->request->getParam('receiver-email-for-filtration', null));
            $ownerEmailForFiltration = trim(Yii::app()->request->getParam('owner_email_for_filtration', null));
            $invite_id = trim(Yii::app()->request->getParam('invite_id'));
            $exceptDevelopersFiltration = (bool)trim(Yii::app()->request->getParam('except-developers', true));
            $simulationScenario = Yii::app()->request->getParam('filter_scenario_id');
            $isInviteCrashed = Yii::app()->request->getParam('is_invite_crashed');

            // remaking email form
            //if ($isReloadRequest) {
                if (null !== $receiverEmailForFiltration) {
                    $filter_form['filter_email'] = $receiverEmailForFiltration;
                }
                else {
                    $filter_form['filter_email'] = "";
                }
            //}

            //if ($isReloadRequest) {
                if (null !== $ownerEmailForFiltration) {
                    $filter_form['owner_email'] = $ownerEmailForFiltration;
                }
                else {
                    $filter_form['owner_email'] = "";
                }
            //}

            //if ($isReloadRequest) {
                if (null !== $exceptDevelopersFiltration) {
                    $filter_form['exceptDevelopersFiltration'] = $exceptDevelopersFiltration;
                }
                else {
                    $filter_form['exceptDevelopersFiltration'] = "";
                }
            //}

            //if ($isReloadRequest) {
                if (null !== $simulationScenario) {
                    $filter_form['filter_scenario_id'] = $simulationScenario;
                }
                else {
                    $filter_form['filter_scenario_id'] = "";
                }
            //}

            //if ($isReloadRequest && null == $invite_id) {
                if (null !== $invite_id) {
                    $filter_form['invite_id'] = $invite_id;
                }
                else {
                   $filter_form['invite_id'] = "";
                }
            //}

            //if ($isReloadRequest) {
                if (null !== $isInviteCrashed) {
                    $filter_form['is_invite_crashed'] = $isInviteCrashed;
                }
                else {
                   $filter_form['is_invite_crashed'] = "";
                }
            //}

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


            if (isset($filter_form['is_invite_crashed']) && $filter_form['is_invite_crashed'] != "") {
                if (false === $previousConditionPresent) {
                    $previousConditionPresent = true;
                } else {
                    $condition .= " AND ";
                }
                $condition .= " is_crashed = " . $filter_form['is_invite_crashed'];
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
                $condition .= " t.email NOT LIKE '%gty1991%' ".
                    " AND t.email NOT LIKE '%@skiliks.com' ".
                    " AND t.email NOT LIKE '%@rmqkr.net' ".
                    " AND sent_time > '2013-06-01 00:00:00' ".
                    " AND t.email NOT IN (".implode(',', UserService::$developersEmails).") ";
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
                else {
                    $condition .= " AND ";
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
            throw new Exception('Файл-шаблон для документа D1 не найден');
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
            throw new Exception(sprintf('Файл %s не найден', $filePath));
        }

        $filename = $sim_id . '_' . $documentTemplate->fileName;
        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $xls;
    }

    /**
     * @throws LogicException
     */
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

    /**
     * Список заказов
     */
    public function actionOrders()
    {
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

        if($done !== null && $notDone === null) {
            $criteria->addCondition("t.paid_at IS NOT NULL");
        } elseif ($done === null && $notDone !== null) {
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
     *
     */
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

    /**
     *
     */
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

    /**
     *
     */
    public function actionInviteCalculateTheEstimate() {

        $simId = Yii::app()->request->getParam('sim_id', null);
        $email = strtolower(str_replace(' ', '+', Yii::app()->request->getParam('email', null)));
        SimulationService::CalculateTheEstimate($simId, $email);

        $this->redirect("/admin_area/invites");
    }

    /**
     *
     */
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
            'invite'        => $invite,
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

    /**
     * @param $simId
     */
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
        $pager->route = 'admin_area/AdminPages/CorporateAccountList';
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
            'order' => 'id DESC',
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
                UserService::logAccountAction($siteUser, $_SERVER['REMOTE_ADDR'], 'Пароль для пользователя '.$siteUser->profile->email.' был изменён админом '.$this->user->profile->email);
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

    public function actionStatisticUserBlockedAuthorization()
    {
        $total = (int)YumUser::model()->countByAttributes(['is_password_bruteforce_detected'=>YumUser::IS_PASSWORD_BRUTEFORCE_DETECTED]);
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

    public function actionUserDetailsByEmail() {
        $email = Yii::app()->request->getParam('email');
        $email = trim($email);
        $profile = YumProfile::model()->findByAttributes(['email' => $email]);

        if (null === $profile) {
            Yii::app()->user->setFlash('error', sprintf('Не найден пользователь с email "%s".' ,$email));
            $this->redirect('/admin_area');
        }

        $this->redirect(sprintf('/admin_area/user/%s/details/', $profile->user_id));
    }

    public function actionUserDetails($userId)
    {
        /* @var $user YumUser */
        $user = YumUser::model()->findByPk($userId);

        if (null === $user) {
            Yii::app()->user->setFlash('error', sprintf('Пользователь с ID = %s не найден', $userId));
            $this->redirect('/admin_area/users');
        }

        if($user->isCorporate()) {

            if($this->getParam('save_form') === 'true'){

                $user->account_corporate->industry_for_sales = $this->getParam('industry_for_sales');
                $user->account_corporate->company_name_for_sales = $this->getParam('company_name_for_sales');
                $user->account_corporate->site = $this->getParam('site');
                $user->account_corporate->description_for_sales = $this->getParam('description_for_sales');
                $user->account_corporate->contacts_for_sales = $this->getParam('contacts_for_sales');
                $user->account_corporate->status_for_sales = $this->getParam('status_for_sales');
                $user->account_corporate->save(false);
            }

            if($this->getParam('discount_form') === 'true') {
                $user->account_corporate->discount = $this->getParam('discount');
                $user->account_corporate->start_discount = $this->getParam('start_discount');
                $user->account_corporate->end_discount = $this->getParam('end_discount');
                if($user->account_corporate->validate(['discount', 'start_discount', 'end_discount'])){
                    $user->account_corporate->save(false);
                    Yii::app()->user->setFlash('success', 'Сохранено успешно');
                }else{
                    $error_message = '';
                    foreach($user->account_corporate->getErrors() as $error){
                        $error_message .= implode('<br>', $error).'<br>';
                    }
                    Yii::app()->user->setFlash('error', $error_message);
                }

            }
        }

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/user_details', [
            'user' => $user,
        ]);
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

        $user->getAccount()->changeInviteLimits($value, $admin);

        // set invites_limit }


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

//        UserService::logCorporateInviteMovementAdd(
//            'Cheats: actionIncreaseInvites',
//            $user->getAccount(),
//            $initValue
//        );

        Yii::app()->user->setFlash('success', "Вам добавлено 10 приглашений!");

        $this->redirect('/admin_area/dashboard');
    }


    public function actionRegistrationList() {
        // getting registration by day
        $userCounter = new СountRegisteredUsers();
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
        $userCounter = new СountRegisteredUsers();
        $userCounter->getAllUserForMonths();
        $userCounter->getNonActiveUsersForMonths();

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
        $userCounter = new СountRegisteredUsers();
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

        $registrationsByYearOld = [];
        $day--;
        $registrationsByYearOld[$day]['period'] = $day;
        $registrationsByYearOld[$day]['totalRegistrations'] = isset($userCounter->totalRegistrations[$day]) ? $userCounter->totalRegistrations[$day] : 0;
        $registrationsByYearOld[$day]['totalPersonals'] = isset($userCounter->totalPersonals[$day]) ? $userCounter->totalPersonals[$day] : 0;
        $registrationsByYearOld[$day]['totalCorporate'] = isset($userCounter->totalCorporate[$day]) ? $userCounter->totalCorporate[$day] : 0;
        $registrationsByYearOld[$day]['totalNonActivePersonals'] = isset($userCounter->totalNonActivePersonals[$day]) ? $userCounter->totalNonActivePersonals[$day] : 0;
        $registrationsByYearOld[$day]['totalNonActiveCorporate'] = isset($userCounter->totalNonActiveCorporate[$day]) ? $userCounter->totalNonActiveCorporate[$day] : 0;
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/registrationCounterList',
            [
                'registrationsByDay'     => $registrationsByDay,
                'registrationsByMonth'   => $registrationsMonth,
                'registrationsByYear'    => $registrationsByYear,
                'registrationsByYearOld'    => $registrationsByYearOld,
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
        $filterSenderEmail = Yii::app()->request->getParam('sender_email', null);

        if($filterSenderEmail !== null) {
            $filterSenderEmail = trim($filterSenderEmail);
            $criteria->addSearchCondition("t.sender_email", $filterSenderEmail);
        }
        // sender_email }

        $filterRecipients = Yii::app()->request->getParam('recipients', null);

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

            $assetsUrl = $this->getAssetsUrl();

            // подмена путей к картинкам на настоящие
            $email->body = str_replace('cid:anjela_long',     $assetsUrl . '/img/site/emails/anjela_long.png',    $email->body);
            $email->body = str_replace('cid:bottom_long',     $assetsUrl . '/img/site/emails/bottom_long.png',    $email->body);
            $email->body = str_replace('cid:denejnaia_long',  $assetsUrl . '/img/site/emails/denejnaia_long.png', $email->body);
            $email->body = str_replace('cid:fikus_long',      $assetsUrl . '/img/site/emails/fikus_long.png',     $email->body);
            $email->body = str_replace('cid:jeleznij_long',   $assetsUrl . '/img/site/emails/jeleznij_long.png',  $email->body);
            $email->body = str_replace('cid:trudiakin_long',  $assetsUrl . '/img/site/emails/trudiakin_long.png', $email->body);
            $email->body = str_replace('cid:krutko_long',     $assetsUrl . '/img/site/emails/krutko_long.png', $email->body);

            $email->body = str_replace('cid:anjela',     $assetsUrl . '/img/site/emails/anjela.png',        $email->body);
            $email->body = str_replace('cid:bottom',     $assetsUrl . '/img/site/emails/bottom.png',        $email->body);
            $email->body = str_replace('cid:denejnaia',  $assetsUrl . '/img/site/emails/denejnaia.png',     $email->body);
            $email->body = str_replace('cid:fikus',      $assetsUrl . '/img/site/emails/fikus.png',         $email->body);
            $email->body = str_replace('cid:jeleznij',   $assetsUrl . '/img/site/emails/jeleznij.png',      $email->body);
            $email->body = str_replace('cid:trudiakin',  $assetsUrl . '/img/site/emails/trudiakin.png',     $email->body);
            $email->body = str_replace('cid:krutko',     $assetsUrl . '/img/site/emails/krutko.png',     $email->body);

            $email->body = str_replace('cid:top-left',   $assetsUrl . '/img/site/emails/top-left.png',      $email->body);
            $email->body = str_replace('cid:skiliks_ny', $assetsUrl . '/img/site/emails/ny/skiliks_ny.jpg', $email->body);

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
        /* @var $assessments AssessmentOverall[] */
        $assessments = AssessmentOverall::model()->with('sim', 'sim.user', 'sim.user.profile') ->findAll([
            'condition' => $condition,
            'order'     => ' t.value DESC '
        ]);

        $simulations = [];
        foreach ($assessments as $assessment) {
            if($assessment->sim->invite !== null) {
                $simulations[] = $assessment->sim;
            }
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
        $worksheet->setCellValueByColumnAndRow(7, 1, "Оценка звёзды");
        $worksheet->setCellValueByColumnAndRow(8, 1, "Процентиль");

        $i = 3;
        foreach ($assessments as $assessment) {
            $worksheet->setCellValueByColumnAndRow(1, $i, isset($assessment->sim->invite)?$assessment->sim->invite->id:"Нет инвайта" );
            $worksheet->setCellValueByColumnAndRow(2, $i, $assessment->sim->id );
            $worksheet->setCellValueByColumnAndRow(3, $i, $assessment->sim->user->profile->email );
            $worksheet->setCellValueByColumnAndRow(4, $i, $assessment->sim->start );
            $worksheet->setCellValueByColumnAndRow(5, $i, $assessment->sim->end );
            $worksheet->setCellValueByColumnAndRow(6, $i, $assessment->sim->status );
            $worksheet->setCellValueByColumnAndRow(7, $i, $assessment->sim->getOverall() );
            $worksheet->setCellValueByColumnAndRow(8, $i, $assessment->sim->getPercentile() );
            $i++;
        }

        $doc = new \PHPExcel_Writer_Excel2007($xlsFile);
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"percentile.xlsx\"");
        $doc->save('php://output');
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
        UserService::authenticate($user);
//        $identity = new YumUserIdentity($user->username, false);
//        $identity->authenticate(true);
//        Yii::app()->user->login($identity);

        $this->redirect('/dashboard');
    }

    public function actionBanUser($userId) {

        $banUser = YumUser::model()->findByPk($userId);

        if($banUser->isCorporate()) {
            $isBanned = $banUser->banUser();
            if($isBanned) {
                Yii::app()->user->setFlash('success', 'Аккаунт '. $banUser->profile->email .' успешно заблокирован.');
            }
        }
    }
    public function actionNotCorporateEmails(){
        $email = new FreeEmailProvider();
        if (Yii::app()->request->isPostRequest) {
            $email->attributes = Yii::app()->request->getParam('FreeEmailProvider');
            if($email->validate(['domain'])){
                $email->save(false, ['domain']);
                $email->validate([]);
            }
        }
        $dataProvider = FreeEmailProvider::model()->searchEmails();

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/not_corporate_emails', ['dataProvider' => $dataProvider, 'email'=>$email]);
    }

    public function actionChangeSecurityRisk() {
        if(null !== $this->getParam('set') && null !== $this->getParam('id')) {

            $email = FreeEmailProvider::model()->findByPk($this->getParam('id'));
            $email->security_risk = $this->getParam('set');
            $email->save(false);
        }

        $this->redirect($this->request->urlReferrer);
    }

    public function actionSiteLogAuthorization() {

        //$dataProvider = FreeEmailProvider::model()->searchEmails();

        $dataProvider = SiteLogAuthorization::model()->searchSiteLogs();

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/site_log_authorization', ['dataProvider' => $dataProvider]);
    }

    public function actionSiteLogAccountAction() {

        //$dataProvider = SiteLogAuthorization::model()->searchSiteLogs();
        $user_id = Yii::app()->request->getParam('user_id');
        $dataProvider = SiteLogAccountAction::model()->searchSiteLogs($user_id);

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/site_log_account_action', ['dataProvider' => $dataProvider]);
    }

    public function actionUserBruteForce() {

        //$dataProvider = SiteLogAuthorization::model()->searchSiteLogs();
        $user_id = Yii::app()->request->getParam('user_id');
        $set = Yii::app()->request->getParam('set');
        /* @var $user YumUser */
        $user = YumUser::model()->findByPk($user_id);
        $user->is_password_bruteforce_detected = $set;
        $user->save(false);

        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionAdminsList()
    {
        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('//admin_area/pages/users_managament/admins_list', [
            'admins'
                => YumUser::model()->findAllByAttributes(['is_admin' => 1]),

            // 1 - action "start_dev_mode"
            'devPermissions'
                => YumPermission::model()->findAllByAttributes(['action' => 1]),
        ]);
    }

    public function actionUserBlockedAuthorizationList() {

        /* @var YumUser[] $users */
        $users = YumUser::model()->findAllByAttributes(['is_password_bruteforce_detected'=>YumUser::IS_PASSWORD_BRUTEFORCE_DETECTED]);
        $this->layout = '/admin_area/layouts/admin_main';

        $this->render('//admin_area/pages/users_managament/blocked-authorization-list', ['users'=>$users]);
    }

    public function actionExportAllCorporateUserXLSX() {
        $export = new CorporateAccountExport();
        $export->export();
    }

    public function actionSendInvites($userId) {

        /* @var YumUser $user */
            $user = YumUser::model()->findByPk($userId);
            $this->layout = '/admin_area/layouts/admin_main';
            $render = ['user'=>$user];
            $list = [];
            $invites = [];
            $hasErrors = false;
            $isValid = false;
            $isSend = false;
            $valid_emails = [];
            $no_valid_emails = [];
            $invite_limit_error = false;

            if( $this->getParam('valid_form') === 'true' ) {
                $isValid = true;
                $data = $this->getParam('data');
                $data['hide_result'] = isset($data['hide_result'])?$data['hide_result']:0;
                $list_email = preg_split("/[\s,]+/", $data['email'], null, PREG_SPLIT_NO_EMPTY);
                $list_first_name = preg_split("/[\s,]+/", $data['first_name'], null, PREG_SPLIT_NO_EMPTY);
                $list_last_name = preg_split("/[\s,]+/", $data['last_name'], null, PREG_SPLIT_NO_EMPTY);
                $list_iteration = count(max($list_email, $list_first_name, $list_last_name));

                for ($i = 0; $i < $list_iteration; $i++) {
                    $email = isset($list_email[$i])?$list_email[$i]:'';
                    if(!empty($email)) {
                        if(in_array($email, $valid_emails)){
                            $no_valid_emails[] = $email;
                        }else{
                            $valid_emails[] = $email;
                        }
                    }

                    $invite = new Invite();
                    $invite->vacancy_id = $data['vacancy'];
                    $invite->email = isset($list_email[$i])?$list_email[$i]:'';
                    $invite->lastname = isset($list_last_name[$i])?$list_last_name[$i]:'';
                    $invite->firstname = isset($list_first_name[$i])?$list_first_name[$i]:'';
                    $invite->message = $data['message'];
                    $profile = YumProfile::model()->findByAttributes(['email' => strtolower($invite->email)]);

                    if($this->getParam('send_form') === 'true') {
                        $isSend = true;
                        $profile_personal = $profile;

                        if(null === $profile_personal) {
                            $password = UserService::generatePassword(8);
                            $user_personal  = new YumUser('registration');
                            $user_personal->setAttributes(['password'=>$password, 'password_again'=>$password, 'agree_with_terms'=>'yes']);
                            $profile_personal  = new YumProfile('registration');
                            $profile_personal->setAttributes(['firstname'=>$invite->lastname, 'lastname'=>$invite->firstname, 'email'=>$invite->email]);
                            $account_personal = new UserAccountPersonal('personal');

                            if(UserService::createPersonalAccount($user_personal, $profile_personal, $account_personal)){

                                YumUser::activate($profile_personal->email, $user_personal->activationKey);
                                try{
                                    if(UserService::sendInvite($user, $invite, $data['hide_result'])){
                                        UserService::sendEmailInviteAndRegistration($invite, $password);
                                    }
                                } catch(RedirectException $e) {
                                    $invite_limit_error = true;

                                }
                            }
                        } else {
                            try{
                                if(UserService::sendInvite($user, $invite, $data['hide_result'])){
                                    UserService::sendEmailInvite($invite);
                                }
                            } catch(RedirectException $e) {
                                $invite_limit_error = true;
                            }
                        }
                    } else {
                        try{
                            UserService::sendInvite($user, $invite, $data['hide_result'], false);
                        } catch(RedirectException $e) {
                            $invite_limit_error = true;
                        }
                    }
                    if($invite->hasErrors()){
                        $hasErrors = true;
                    }
                    $invites[] = $invite;
                }

                $render['data'] = (object)$data;
            } else {
                $render['data'] = (object)['email'=>'','first_name'=>'','last_name'=>'','vacancy'=>'','hide_result'=>'','message'=>$user->account_corporate->default_invitation_mail_text];
            }

        if(count($no_valid_emails) !== 0) {
            $hasErrors = true;
        }

        $render['list'] = $list;
        $render['invites'] = $invites;
        $render['has_errors'] = $hasErrors;
        $render['isValid'] = $isValid;
        $render['isSend'] = $isSend;

        if($hasErrors) {
            if(count($no_valid_emails) !== 0) {
                Yii::app()->user->setFlash('error', 'Дублирование email-ов '.implode(', ', $no_valid_emails));
            }else{
                Yii::app()->user->setFlash('error', Yii::t('site', 'Исправьте ошибки'));
            }
        }else{
            if($isValid) {
                if($isSend){
                    Yii::app()->user->setFlash('success', Yii::t('site', 'Все приглашения отправлены в очередь писем'));
                } else {
                    if(count($invites) === 0 && $isValid) {
                        $render['has_errors'] = true;
                        Yii::app()->user->setFlash('error', Yii::t('site', 'У вас нет адресатов'));
                    }elseif($user->account_corporate->getTotalAvailableInvitesLimit() < count($invites)){
                        $invite_limit_error = true;
                    }else{
                        Yii::app()->user->setFlash('success', Yii::t('site', 'Все поля правильные'));
                    }
                }
            }
        }
        if($invite_limit_error){
            $render['has_errors'] = true;
            Yii::app()->user->setFlash('error', Yii::t('site', 'У вас недостаточно инвайтов(сейчас '.$user->account_corporate->getTotalAvailableInvitesLimit().' - нужно '.count($invites).')'));
        }
        $this->render('//admin_area/pages/user_send_invites', $render);
    }

    /**
     * Позволяет пользователю скачать
     * protected/system_data/analytic_files_2/full_report_.xlsx
     */
    public function actionDownloadFullAnalyticFile() {
        /**
         * @link: http://filext.com/faq/office_mime_types.php
         */

        $filename = 'analitics_'.date('Ymd').'_admin_version.xlsx';

        header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        echo file_get_contents(
            Yii::app()->basePath.'/system_data/analytic_files_2/full_report_.xlsx'
        );
        Yii::app()->end();
    }
}