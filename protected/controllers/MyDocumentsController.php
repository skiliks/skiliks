<?php

class MyDocumentsController extends SimulationBaseController
{

    /**
     * Получение списка документов
     */
    public function actionGetList()
    {
        $simulation = $this->getSimulationEntity();

        $json = [
            'result' => self::STATUS_SUCCESS,
            'data'   => MyDocumentsService::getDocumentsList($simulation),
        ];
        $this->sendJSON($json);
    }

    /**
     * Добавление 
     */
    public function actionAdd()
    {
        $simulation = $this->getSimulationEntity();
        
        $fileId = (int) Yii::app()->request->getParam('attachmentId');
        $file   = MyDocument::model()->findByPk($fileId);

        $json = [
            'result' => self::STATUS_SUCCESS,
            'status' => MyDocumentsService::makeDocumentVisibleInSimulation($simulation, $file),
            'file'   => [
                'id'   => $file->id,
                'name' => $file->fileName,
                'mime' => $file->template->getMimeType(),
            ]
        ];
        $this->sendJSON($json);
    }

    /**
     * New code!
     * @autor Slavka
     * @return
     */
    public function actionGetExcel()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $limit = 10;
        $n = 0 ;
        $simulation = $this->getSimulationEntity();

        $id = Yii::app()->request->getParam('id', NULL);
        $file = MyDocument::model()->findByAttributes(['sim_id' => $simulation->id, 'id' => $id]);
        assert($file);
        $zoho = new ZohoDocuments($simulation->primaryKey, $file->primaryKey, $file->template->srcFile, 'xls', $file->fileName);
        $errors = [];
        $responses = [];

        while (null === $zoho->getUrl() && $n < $limit ) {
            try {
                $n++;
                $zoho->sendDocumentToZoho();
                $responses[] = str_replace("\r", '', str_replace("\n", '.', $zoho->response));
            } catch(LogicException $e) {
                $errors[] = str_replace("\r", '', str_replace("\n", '.', $e->getMessage()));
                if ($n === $limit) {
                    throw $e;
                }
            }
        }

        $result = [
            'result'           => self::STATUS_SUCCESS,
            'filedId'          => $file->id,
            'excelDocumentUrl' => $zoho->getUrl(),
            'errors'           => $errors,
            'responses'        => $responses,
            'fn1'              => $file->template->srcFile,
            'fn2'              => $file->fileName,
        ];
        $this->sendJSON($result);
    }

    /**
     * Is document vas saved by Zoho at list once
     */
    public function actionIsDocumentSaved()
    {
        $simulation = $this->getSimulationEntity();

        $id = Yii::app()->request->getParam('id', NULL);
        /** @var MyDocument $file */
        $file = MyDocument::model()->findByAttributes(['sim_id' => $simulation->id, 'id' => $id]);
        $zoho = new ZohoDocuments($simulation->primaryKey, $file->primaryKey, $file->template->srcFile, 'xls', $file->fileName);
        $json = [
            'status' => (int)($zoho->checkIsUserFileExists() && $file->is_was_saved),
            'IsUserFileExists' => $zoho->checkIsUserFileExists(),
            'is_was_saved' => $file->is_was_saved,
            'id' => $id,
            'id_2' => $file->id,
        ];

        if ($json['is_was_saved']) {
            SimulationService::LogAboutSim($simulation, 'sim: zoho verification passed');
        } else {
            SimulationService::LogAboutSim($simulation, 'sim: zoho verification failed');
        }

        $this->sendJSON($json);
    }
}
