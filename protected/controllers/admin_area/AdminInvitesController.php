<?php

class AdminInvitesController extends BaseAdminController {

    /**
     * @param integer $inviteId
     */
    public function actionChangeSimId($inviteId) {

        /** @var Invite $invite */
        $invite = Invite::model()->findByPk($inviteId);

        /** @var Simulation $simulation */
        $simId = Yii::app()->request->getParam('simId');
        $simulation = Simulation::model()->findByPk($simId);

        if (null == $invite) {
            Yii::app()->user->setFlash(
                'error',
                'Приглашения №'.$inviteId.' не существует.'
            );
            $this->redirect('/admin_area/invites');
        }

        if (null == $simulation) {
            Yii::app()->user->setFlash(
                'error',
                'Симуляции №'.$simId.' не существует.'
            );
            $this->redirect('/admin_area/invites');
        }

        $oldSimId = $invite->simulation_id;
        $invite->simulation_id = $simId;
        $invite->save();

        Yii::app()->user->setFlash(
            'success',
            sprintf(
                'Симуляции №%s (тип %s, %s) успершно связана с приглашением №%s от %s.',
                $simId,
                $simulation->game_type->slug,
                (null !== $simulation->end) ? 'завершена ' . $simulation->end : 'не пройдена',
                $inviteId,
                $invite->ownerUser->profile->email
            )
        );

        InviteService::logAboutInviteStatus(
            $invite,
            sprintf(
                'Админ %s сменил симуляцию у пригашения %s с %s на %s.',
                $this->user->profile->email,
                $inviteId,
                $oldSimId,
                $simId
            )
        );

        $this->redirect('/admin_area/invite/'.$inviteId.'/site-logs');
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

        $models = Invite::model()->findAll($criteria);

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

    public function actionIncreaseInvites()
    {
        $user = Yii::app()->user->data();

        if (false == $user->isCorporate()) {
            $this->redirect('/admin_area/dashboard');
        }

        $user->getAccount()->invites_limit += 10;
        $user->getAccount()->save();

        Yii::app()->user->setFlash('success', "Вам добавлено 10 приглашений!");

        $this->redirect('/admin_area/dashboard');
    }

    /**
     *
     */
    public function actionSiteLogs() {
        $invite_id = Yii::app()->request->getParam('invite_id', null);
        /** @var Invite $invite */
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
            'simulations'   => Simulation::model()->findAllByAttributes(
                ['user_id' => $invite->receiver_id]
            ),
        ]);
    }
} 