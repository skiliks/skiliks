<?php

class AdminPrbbController extends BaseAdminController {

    /**
     *
     */
    public function actionImageArchivesList()
    {
        if (false == Yii::app()->user->data()->can('support_prbb_generete_download')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $currentTask = SiteLogGeneratePrbbFiles::model()->findByAttributes(['finished_at' => null]);
        $allTasks = SiteLogGeneratePrbbFiles::model()->findAll(['order' => 'id DESC']);

        $simulationIds = [];

        if (null == $currentTask
            && 'generate' == Yii::app()->request->getParam('action')
            && null != Yii::app()->request->getParam('simIds')) {

            ini_set('max_execution_time', 60*60*1); // 60 min

            $log = new SiteLogGeneratePrbbFiles();
            $log->started_by_id = Yii::app()->user->data()->id;
            $log->started_at = date('Y-m-d H:i:s');
            $log->save();

            $log->result .= 'Переданные ид симуляций: ' . Yii::app()->request->getParam('simIds') . '.' . "<br/>";
            $log->save();

            $simulationIds = explode(',', Yii::app()->request->getParam('simIds'));

            $results = [];

            foreach($simulationIds as $sim_id){
                if(!empty($sim_id)) {

                    $log->result .= 'Обрабатываю симуляцию ' . $sim_id . '.' . "<br/>";
                    $log->save();

                    $simulation = Simulation::model()->findByPk(trim($sim_id));
                    if($simulation !== null){

                        $log->result .= 'Симуляция ' . $sim_id . ' найдена в БД.' . "<br/>";
                        $log->save();

                        $results[] = SimulationService::saveAssessmentPDFFilesOnDisk($simulation);
                        $log->result .= 'JPG-и для симуляции ' . $sim_id . ' готовы.' . "<br/>";
                        $log->save();
                    }
                }
            }
            $first = reset($results);

            // ну и ещё надо в один архив их собрать
            $chains = explode('/', $first['pathToFolder']);
            $pathToZip = str_replace(
                    $chains[count($chains) - 2] . '/',
                    '',
                    $first['pathToFolder']
                ) . 'archive_' . $log->id . '.zip';
            $log->path = $pathToZip;
            $log->save();

            $zip = new ZipArchive();
            $log->result .= 'Инициирую архив.';
            $log->save();

            if (file_exists($pathToZip)) {
                $zip->open($pathToZip, ZIPARCHIVE::OVERWRITE);
            } else {
                $zip->open($pathToZip, ZIPARCHIVE::CREATE);
            }

            foreach ($results as $result) {
                foreach ($result['pathToFiles'] as $pathToFile) {
                    $chains = explode('/', $pathToFile['jpg']);
                    // в результате все файлы в архиве будут правильно проименованы
                    // и разложены в папки типа "MihailBoyarskiy/sp1_1356_Boyarskiy_overall.jpg"
                    $zip->addFile(
                        $pathToFile['jpg'],
                        $chains[count($chains) - 2] . '/' . $chains[count($chains) - 1]
                    );
                }
            }

            $zip->close();
            $log->result .= 'Архив готов.' . "<br/>";
            $log->save();

            $log->finished_at = date('Y-m-d H:i:s');
            $log->save();
        }

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/prbb/image_archive_list', [
            'user'           => $this->user,
            'currentTask'    => $currentTask,
            'allTasks'       => $allTasks,
            'simulationIds' => $simulationIds,
        ]);
    }

    /**
     * @param integer $logId
     */
    public function actionDownloadImagesArchive($logId)
    {
        if (false == Yii::app()->user->data()->can('support_prbb_generete_download')) {
            Yii::app()->user->setFlash('error', 'У вас не достаточно прав.');
            $this->redirect('/admin_area/dashboard');
        }

        $log = SiteLogGeneratePrbbFiles::model()->findByPk($logId);

        if (null !== $log && file_exists($log->path)) {
            $zipFile = file_get_contents($log->path);
        } else {
            Yii::app()->user->setFlash('error', 'Файл №' . $log->id . ' не найден.');
            $this->redirect('/admin_area/prbb/images-list');
        }

        header('Content-Type: application/zip; charset=utf-8');
        header('Content-Disposition: attachment; filename="archive_' . $log->id . '.zip"');

        echo $zipFile;
    }
} 