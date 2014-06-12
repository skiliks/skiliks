<?php

class AdminProjectConfigController extends BaseAdminController {

    public function actionProjectConfigsList() {
        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/project_config/configs_list', [
            'user'    => $this->user,
            'configs' =>  ProjectConfig::model()->findAll(),
        ]);
    }

    public function actionAddConfig() {
        $id = Yii::app()->request->getParam('id');
        $action = Yii::app()->request->getParam('action');

        /** @var ProjectConfig $config */
        if (null !== $id) {
            $config = ProjectConfig::model()->findByPk($id);
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
    public function actionConfigLogsList($id) {
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