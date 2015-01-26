<?php

class AdminPagesController extends BaseAdminController {

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

    public function actionLiveSimulations()
    {
        if (false == Yii::app()->user->data()->can('online_sim_list_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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



    public function actionSimulationDetail()
    {
        if (false == Yii::app()->user->data()->can('sim_results_popup_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $sim_id = Yii::app()->request->getParam('sim_id', null);
        @ Yii::app()->request->cookies['display_result_for_simulation_id'] =
            new CHttpCookie('display_result_for_simulation_id', $sim_id);

        $this->redirect('/dashboard');
    }

    public function actionGetBudget()
    {
        if (false == Yii::app()->user->data()->can('sim_logs_and_d1_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
    public function actionResetInvite()
    {
        if (false == Yii::app()->user->data()->can('invite_roll_back')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $invite_id = Yii::app()->request->getParam('invite_id', null);
        $invite = Invite::model()->findByPk($invite_id);
        /** @var Invite $invite */
        if (empty($invite)) {
            throw new LogicException('Invite does not exist');
        }
        InviteService::logAboutInviteStatus($invite, 'Админ '.$this->user->profile->email.' начал откат приглашения id = '.$invite_id);
        $result = $invite->resetInvite();
        if(false === $result){
            throw new LogicException("The operation is not successful");
        }
        InviteService::logAboutInviteStatus($invite, 'Админ '.$this->user->profile->email.' откатил приглашение id = '.$invite_id);
        Yii::app()->user->setFlash('success', "Успешно");
        $this->redirect($this->request->urlReferrer);
    }

    /**
     * Chande invite status
     * @throws Exception
     */
    public function actionInviteActionStatus() {
        if (false == Yii::app()->user->data()->can('invite_status_change')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
            InviteService::logAboutInviteStatus($invite, 'Админ '.$this->user->profile->email.' изменил статус с '.Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($invite->status));
        } else {
            throw new Exception("Status not found");
        }

        $this->redirect("/admin_area/invites");
    }

    /**
     *
     */
    public function actionInviteCalculateTheEstimate()
    {
        if (false == Yii::app()->user->data()->can('invite_recalculate')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $simId = Yii::app()->request->getParam('sim_id', null);
        $email = strtolower(str_replace(' ', '+', Yii::app()->request->getParam('email', null)));
        SimulationService::CalculateTheEstimate($simId, $email);

        /** @var Simulation $simulation */
        $simulation = Simulation::model()->findByPk($simId);

        Yii::app()->user->setFlash('success', "Приглашение успешно пересчитано.");

        $this->redirect('/admin_area/invite/' . $simulation->invite->id . '/site-logs');
    }

    /**
     *
     */
    public function actionSimSiteLogs()
    {
        if (false == Yii::app()->user->data()->can('sim_site_logs_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('sim_on_off_emergency_panel')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('simulations_list_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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

        // white list {
        $emails = [];
        if (null != Yii::app()->user->data()->emails_white_list) {
            $emails = explode(
                ',',
                str_replace(' ', '', Yii::app()->user->data()->emails_white_list)
            );
        }

         if (0 < count($emails)) {
            $criteria->join = ' LEFT JOIN user AS user ON t.user_id = user.id LEFT JOIN profile AS profile ON profile.user_id = user.id ';
            $criteria->addInCondition('profile.email', $emails);

            $allFilters['condition'] .= sprintf(" AND profile.email IN ('%s') ", implode("','", $emails));
        }
        // white list }

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
        if (false == Yii::app()->user->data()->can('all_users_list_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $this->pageTitle = 'Админка: Список пользователей';
        $this->layout = '//admin_area/layouts/admin_main';

        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

        $criteria = new CDbCriteria;

        // white list {
        $emails = [];
        if (null != Yii::app()->user->data()->emails_white_list) {
            $emails = explode(
                ',',
                str_replace(' ', '', Yii::app()->user->data()->emails_white_list)
            );
        }

        $condition = ' user_id > 0 ';
        if (0 < count($emails)) {
            $criteria->addInCondition('t.email', $emails);

            $condition = sprintf(" t.email IN ('%s') ", implode("','", $emails));
        }
        // white list }

        $totalItems = YumProfile::model()->count($criteria);
        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/UsersList';
        // pager }

        $this->render('/admin_area/pages/users_table', [
            'profiles' => YumProfile::model()->findAll([
                'condition' => $condition, // white list
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
        if (false == Yii::app()->user->data()->can('corp_users_list_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        // pager {
        $page = Yii::app()->request->getParam('page');

        if (null === $page) {
            $page = 1;
        }

        $criteria = new CDbCriteria;

        // white list {
        $emails = [];
        if (null != Yii::app()->user->data()->emails_white_list) {
            $emails = explode(
                ',',
                str_replace(' ', '', Yii::app()->user->data()->emails_white_list)
            );
        }

        $condition = ' user.id > 0 ';
        if (0 < count($emails)) {
            $criteria->join = ' LEFT JOIN user u ON t.user_id = u.id ';
            $criteria->addInCondition('u.emails_white_list', $emails);

            $condition = sprintf(" profile.email IN ('%s') ", implode("','", $emails));
        }
        // white list }

        $totalItems = UserAccountCorporate::model()->count($criteria);
        $pager = new CustomPagination($totalItems);
        $pager->pageSize = $this->itemsOnPage;
        $pager->applyLimit($criteria);
        $pager->route = 'admin_area/AdminPages/CorporateAccountList';
        // pager }

        $this->pageTitle = 'Админка: Список корпоративных аккаунтов';
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/corporate_accounts_table', [
            'accounts' => UserAccountCorporate::model()
                ->with('user', 'user.profile')
                ->findAll([
                'condition' => $condition, // white list
                "limit"     => $this->itemsOnPage,
                "offset"    => ($page-1)*$this->itemsOnPage,
                "order"     => ' t.user_id DESC ',
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
        if (false == Yii::app()->user->data()->can('user_invite_movement_logs_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('user_change_password')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('sim_server_requests_list_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('statistic_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('invites_allow_restart_finished_simulation')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }
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

    /**
     * Список отзывов, а также обработка фор редактирования комментириев к отзывам
     * и отмечания отзыва прочитанным
     */
    public function actionFeedBacksList()
    {
        if (false == Yii::app()->user->data()->can('feedback_view_edit')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        // редактирование комментария к отзыву
        if($this->getParam('is_ajax') === 'yes'){
            $feedback = Feedback::model()->findByPk($this->getParam('id'));
            $feedback->comment = $this->getParam('message');
            $feedback->save(false);
            Yii::app()->user->setFlash('success', "Успешно");
            return;
        }

        // ставим пометку что отзыв обработан
        if($this->getParam('is_action') === 'yes'){
            $feedback = Feedback::model()->findByPk($this->getParam('id'));
            $feedback->is_processed = $this->getParam('is_processed');
            $feedback->save(false);
            Yii::app()->user->setFlash('success', "Успешно");
            $this->redirect('/admin_area/feedbacks');
        }

        $this->pageTitle = 'Админка: Список отзывов';
        $this->layout = '//admin_area/layouts/admin_main';

        $feedbacks = Feedback::model()->findAll([
            "order" => 'id DESC',
        ]);

        foreach ($feedbacks as $feedback) {
            $feedback->refresh();
        }

        $this->render('/admin_area/pages/feedbacks_table', [
            'feedbacks' => $feedbacks,
        ]);
    }

    /**
     * Список подписавшихся на рассылку
     */
    public function actionSubscribersList()
    {
        if (false == Yii::app()->user->data()->can('subscribers_list_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $emails = Yii::app()->db->createCommand()
            ->select( 'id, email' )
            ->from( 'emails_sub' )
            ->queryAll();

        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/subscribers_table', [
            'subscribersEmails' => $emails,
        ]);
    }

    public function actionUserDetailsByEmail()
    {
        $email = Yii::app()->request->getParam('email');
        $email = trim($email);
        $profile = YumProfile::model()->findByAttributes(['email' => $email]);

        if (null === $profile) {
            Yii::app()->user->setFlash('error', sprintf('Не найден пользователь с email "%s".' ,$email));
            $this->redirect('/admin_area');
        }

        $this->redirect(sprintf('/admin_area/user/%s/details/', $profile->user_id));
    }

    /**
     * @param integer $userId
     */
    public function actionUserDetails($userId)
    {
        /* @var $user YumUser */
        $user = YumUser::model()->findByPk($userId);

        if (null === $user) {
            Yii::app()->user->setFlash('error', sprintf('Пользователь с ID = %s не найден', $userId));
            $this->redirect('/admin_area/users');
        }

        if (false == Yii::app()->user->data()->can('user_details_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        if (null != Yii::app()->user->data()->emails_white_list
            && -1 == strpos(Yii::app()->user->data()->emails_white_list, $user->profile->email)) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/users');
        }

        if($user->isCorporate()) {

            if($this->getParam('save_form') === 'true') {

                if (false == Yii::app()->user->data()->can('user_sales_manager_data_edit')) {
                    Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
                    $this->redirect('/admin_area/dashboard');
                }

                if($user->account_corporate->industry_for_sales !== $this->getParam('industry_for_sales')){
                    $user->account_corporate->industry_for_sales = $this->getParam('industry_for_sales');
                    UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'Админ '.$this->user->profile->email.' указал Отрасль для пользователя '.$this->getParam('industry_for_sales').' для пользователя '.$user->profile->email);
                }
                if($user->account_corporate->company_name_for_sales !== $this->getParam('company_name_for_sales')) {
                    $user->account_corporate->company_name_for_sales = $this->getParam('company_name_for_sales');
                    UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'Админ '.$this->user->profile->email.' указал Название компании для пользователя '.$this->getParam('company_name_for_sales').' для пользователя '.$user->profile->email);
                }
                if($user->account_corporate->site !== $this->getParam('site')) {
                    $user->account_corporate->site = $this->getParam('site');
                    UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'Админ '.$this->user->profile->email.' указал Сайт '.$this->getParam('site').' для пользователя '.$user->profile->email);
                }
                if($user->account_corporate->description_for_sales !== $this->getParam('description_for_sales')){
                    $user->account_corporate->description_for_sales = $this->getParam('description_for_sales');
                    UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'Админ '.$this->user->profile->email.' указал Описание компании для пользователя '.$this->getParam('description_for_sales').' для пользователя '.$user->profile->email);
                }
                if($user->account_corporate->contacts_for_sales !== $this->getParam('contacts_for_sales')) {
                    UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'Админ '.$this->user->profile->email.' указал Контактный телефон для пользователя '.$this->getParam('contacts_for_sales').' для пользователя '.$user->profile->email);
                    $user->account_corporate->contacts_for_sales = $this->getParam('contacts_for_sales');
                }
                if($user->account_corporate->status_for_sales !== $this->getParam('status_for_sales')){
                    UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'Админ '.$this->user->profile->email.' указал Статус контактного лица для пользователя '.$this->getParam('status_for_sales').' для пользователя '.$user->profile->email);
                    $user->account_corporate->status_for_sales = $this->getParam('status_for_sales');
                }
                $user->account_corporate->save(false);

            }

            if($this->getParam('discount_form') === 'true') {
                if (false == Yii::app()->user->data()->can('user_discount_edit')) {
                    Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
                    $this->redirect('/admin_area/dashboard');
                }
                $user->account_corporate->discount = $this->getParam('discount');
                $user->account_corporate->start_discount = $this->getParam('start_discount');
                $user->account_corporate->end_discount = $this->getParam('end_discount');
                if($user->account_corporate->validate(['discount', 'start_discount', 'end_discount'])){
                    $user->account_corporate->save(false);
                    UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'Админ '.$this->user->profile->email.' назначил скидку '.$this->getParam('discount').' с '.$this->getParam('start_discount').' до '.$this->getParam('end_discount').' для пользователя '.$user->profile->email);
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
            'user'  => $user,
            'roles' => YumRole::model()->findAll(),
        ]);
    }

    public function actionImportsList()
    {
        if (false == Yii::app()->user->data()->can('system_make_re_import')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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

    public function actionRegistrationList()
    {
        if (false == Yii::app()->user->data()->can('statistic_registration_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('system_make_re_import')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('support_mail_queue_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        $totalItems = EmailQueue::model()->count($criteria);

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
        if (false == Yii::app()->user->data()->can('sim_rating_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('sim_rating_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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
        if (false == Yii::app()->user->data()->can('user_login_ghost')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав 1.');
            $this->redirect('/admin_area/dashboard');
        }

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

        $adminUser = Yii::app()->user->data();
        // непосредственно "пере-аутентификация"
        UserService::authenticate($user);

        // логируем факт авторизации в лог аккаунта пользователя сайта
        UserService::logAccountAction(
            $user,
            $_SERVER['REMOTE_ADDR'],
            'Администратор '.$adminUser->profile->email.' авторизировался в аккаунте '.$user->profile->email
        );

        UserService::addAuthorizationLog(
            $user->profile->email,
            '--',
            SiteLogAuthorization::SUCCESS_AUTH,
            $user->id,
            SiteLogAuthorization::ADMIN_TO_USER
        );

        $this->redirect('/dashboard');
    }

    public function actionNotCorporateEmails()
    {
        if (false == Yii::app()->user->data()->can('support_free_mail_services_list_view_edit')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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

    /**
     * Отображение лога авторизяции пользователей
     */
    public function actionSiteLogAuthorization() {
        if (false == Yii::app()->user->data()->can('auth_logs_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $dataProvider = SiteLogAuthorization::model()->searchSiteLogs();

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/site_log_authorization', ['dataProvider' => $dataProvider]);
    }

    public function actionSiteLogAccountAction()
    {
        if (false == Yii::app()->user->data()->can('user_logs_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        //$dataProvider = SiteLogAuthorization::model()->searchSiteLogs();
        $user_id = Yii::app()->request->getParam('user_id');
        $dataProvider = SiteLogAccountAction::model()->searchSiteLogs($user_id);

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/site_log_account_action', ['dataProvider' => $dataProvider]);
    }

    public function actionUserBruteForce()
    {
        if (false == Yii::app()->user->data()->can('corp_user_ban_unban')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        //$dataProvider = SiteLogAuthorization::model()->searchSiteLogs();
        $user_id = Yii::app()->request->getParam('user_id');
        $set = Yii::app()->request->getParam('set');
        /* @var $user YumUser */
        $user = YumUser::model()->findByPk($user_id);
        $user->is_password_bruteforce_detected = $set;
        $user->save(false);
        $action = ($set === YumUSer::IS_PASSWORD_BRUTEFORCE_DETECTED)?'заблокирована':'разблокирована';

        UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'У пользователь '.$user->profile->email.' была '.$action.' авторизация админом '.$this->user->profile->email);

        $this->redirect(Yii::app()->request->urlReferrer);
    }

    /**
     * Страница со списком админов и суперАдминов
     */
    public function actionAdminsList()
    {
        if (false == Yii::app()->user->data()->can('admins_list_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        // роли
        $roleAdmin = YumRole::model()->findByAttributes(['title' => 'Админ']);
        $roleSuperAdmin = YumRole::model()->findByAttributes(['title' => 'СуперАдмин']);

        // связи
        $connection = Yii::app()->db;
        $command = $connection->createCommand(sprintf(
            'SELECT user_id FROM `user_role` WHERE role_id = %s; ',
            $roleSuperAdmin->id
        ));
        $superAdmins = $command->queryAll();

        $command = $connection->createCommand(sprintf(
            ' SELECT user_id FROM `user_role` WHERE role_id = %s; ',
            $roleAdmin->id
        ));
        $admins = $command->queryAll();

        // комбинируем
        // комбинируем
        $adminIds = [];
        foreach ($admins as $admin) {
            $adminIds[] = $admin['user_id'];
        }
        unset($admin);
        $superAdminIds = [];
        foreach ($superAdmins as $superAdmin) {
            $superAdminIds[] = $superAdmin['user_id'];
        }
        unset($superAdmin);

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('//admin_area/pages/users_management/admins_list', [
            'admins'      => YumUser::model()->findAllByAttributes(['id' => $adminIds]),
            'superAdmins' => YumUser::model()->findAllByAttributes(['id' => $superAdminIds]),
        ]);
    }

    public function actionUserBlockedAuthorizationList()
    {
        if (false == Yii::app()->user->data()->can('banned_users_list_view')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        /* @var YumUser[] $users */
        $users = YumUser::model()->findAllByAttributes(['is_password_bruteforce_detected'=>YumUser::IS_PASSWORD_BRUTEFORCE_DETECTED]);
        $this->layout = '/admin_area/layouts/admin_main';

        $this->render('//admin_area/pages/users_management/blocked-authorization-list', ['users'=>$users]);
    }

    public function actionExportAllCorporateUserXLSX()
    {
        if (false == Yii::app()->user->data()->can('corp_users_list_export')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $export = new CorporateAccountExport();
        $export->export(Yii::app()->user->data());
    }

    /**
     * Позволяет пользователю скачать
     * protected/system_data/analytic_files_2/full_report_.xlsx
     */
    public function actionDownloadFullAnalyticFile()
    {
        if (false == Yii::app()->user->data()->can('consolidated_analytic_file_generate_download')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

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

    public function actionExcludedFromMailing()
    {
        if (false == Yii::app()->user->data()->can('user_add_remove_from_news_mail_list')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        if(null !== $this->getParam('set') && null !== $this->getParam('user_id')) {

            $account = UserAccountCorporate::model()->findByAttributes(['user_id'=>$this->getParam('user_id')]);
            $account->excluded_from_mailing= $this->getParam('set');
            $account->save(false);
        }

        $this->redirect($this->request->urlReferrer);
    }
}