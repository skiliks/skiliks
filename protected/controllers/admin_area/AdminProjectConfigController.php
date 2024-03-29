<?php

class AdminProjectConfigController extends BaseAdminController {

    public function actionProjectConfigsList()
    {
        if (false == Yii::app()->user->data()->can('system_setting_view_edit')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $configs = ProjectConfig::model()->findAll();

        foreach ($configs as $config) {
            $config->refresh();
        }

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/project_config/configs_list', [
            'user'    => $this->user,
            'configs' => $configs,
        ]);
    }

    public function actionAddConfig()
    {
        if (false == Yii::app()->user->data()->can('system_setting_view_edit')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $id = Yii::app()->request->getParam('id');
        $action = Yii::app()->request->getParam('action');

        /** @var ProjectConfig $config */
        if (null !== $id) {
            $config = ProjectConfig::model()->findByPk($id);
            $config->refresh();
        }

        if (null == $id || null == $config) {
            $config = new ProjectConfig();
            $config->attributes = Yii::app()->request->getParam('ProjectConfig');

            // по странной причини is_use_in_simulation всегда равен "1"
            // приходится задвать "в ручную"
            $config->is_use_in_simulation = $config->attributes['is_use_in_simulation'];
        }

        // Пользователь нажал <button> "Сохранить"
        if (null != $action) {
            $config->attributes = Yii::app()->request->getParam('ProjectConfig');

            // по странной причини is_use_in_simulation всегда равен "1"
            // приходится задвать "в ручную"
            $config->is_use_in_simulation = $config->attributes['is_use_in_simulation'];

            if ($config->validate()) {

                SiteLogProjectConfig::log($this->user, $config);

                $config->save();

                Yii::app()->user->setFlash('success', sprintf(
                    'Парамерт "%s" типа "%s" успешно обновлен (создан) со значением "%s".',
                    $config->alias,
                    $config->type,
                    $config->value
                ));

                $this->redirect('/admin_area/project_configs/list');
            }
        }

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/project_config/add_config', [
            'user'    => $this->user,
            'config'  => $config,
        ]);
    }

    /**
     * @param integer $id, ProjectConfig.id
     */
    public function actionConfigLogsList($id)
    {
        if (false == Yii::app()->user->data()->can('system_setting_view_edit')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $log = SiteLogProjectConfig::model()->findAll([
            'order' => 'created_at DESC',
            'condition' => 'project_config_id = :project_config_id',
            'params' => [
                'project_config_id' => $id
            ]
        ]);

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/project_config/config_logs_list', [
            'user'    => $this->user,
            'config'  => ProjectConfig::model()->findByPk($id),
            'logs'    => $log,
        ]);
    }
} 