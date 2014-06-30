<?php

/**
 * Контроллер сервисных страниц:
 *  - статистика
 *  - консольные команды выполняемые из админки
 *
 * Class AdminServicePagesController
 */
class AdminServicePagesController extends BaseAdminController {

    /**
     * Проверка оценок по всем симуляция на внутреннюю консистентность
     */
    public function actionCheckAssessmentResults() {

        $currentCheck = SiteLogCheckResults::model()->findByAttributes(['finished_at' => null]);
        $allCheckLogs = SiteLogCheckResults::model()->findAll(['order' => 'id DESC']);

        if (null == $currentCheck && 'check' == Yii::app()->request->getParam('action')) {
            ini_set('max_execution_time', 60*60*0.5); // 30 min

            $simulations = Simulation::model()->findAll(
                " results_popup_cache is not null
                and scenario_id = 2
                and start > '2013-08-01 00:00:00'
                and status = 'complete' "
            );

            // симуляций меньше 1000, можно и циклом пройтись
            $userSimulations = [];
            foreach ($simulations as $simulation) {
                if ('tatiana@skiliks.com' == $simulation->user->profile->email
                    || 'tony@skiliks.com' == $simulation->user->profile->email
                    || 'vladimir@skiliks.com' == $simulation->user->profile->email
                    || 'vladimir1@skiliks.com' == $simulation->user->profile->email
                    || 'tetyana.grybok@skiliks.com' == $simulation->user->profile->email
                    || 'sarnavskyi89@gmail.com' == $simulation->user->profile->email
                    || 'andrey.sarnavskiy@skiliks.com' == $simulation->user->profile->email) {
                    continue;
                }
                $userSimulations[] = $simulation;
            }

            unset($simulations);

            /**
             * @var CheckAssessmentResults $component
             */
            $component = new CheckAssessmentResults();
            $component->checkAndFix($userSimulations, false);
        }

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/service/check_assessment_values', [
            'user'         => $this->user,
            'currentCheck' => $currentCheck,
            'allCheckLogs' => $allCheckLogs,
        ]);
    }

    /**
     * Генерация сводного аналитического файлы по всем симуляциям
     */
    public function actionGenerateConsolidatedAnalyticFileResults() {

        if (false == Yii::app()->user->data()->can('consolidated_analytic_file_generate_download')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $generatedFile = SiteLogGenerateConsolidatedAnalyticFile::model()->findByAttributes(['finished_at' => null]);
        $allFiles      = SiteLogGenerateConsolidatedAnalyticFile::model()->findAll(['order' => 'id DESC']);

        if (null == $generatedFile && 'generate' == Yii::app()->request->getParam('action')) {
            /** @var SiteLogGenerateConsolidatedAnalyticFile $log */
            $log = new SiteLogGenerateConsolidatedAnalyticFile();
            $log->started_at = date('Y-m-d H:i:s');
            $log->started_by_id = $this->user->id;
            $log->save();

            SiteLogGenerateConsolidatedAnalyticFile::generate('v2', $log);
            $log->finished_at = date('Y-m-d H:i:s');
            $log->save();
        }

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/service/generate_consolidated_assessment_file', [
            'user'          => $this->user,
            'generatedFile' => $generatedFile,
            'allFiles'      => $allFiles,
        ]);
    }
}