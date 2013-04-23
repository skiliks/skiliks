<?php

/**
 * Контроллер моих документов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsController extends AjaxController
{

    /**
     * Получение списка документов
     */
    public function actionGetList()
    {
        $simulation = $this->getSimulationEntity();
        
        $this->sendJSON(array(
            'result' => 1,
            'data'   => MyDocumentsService::getDocumentsList($simulation),
        ));
    }

    /**
     * Добавление 
     */
    public function actionAdd()
    {
        $simulation = $this->getSimulationEntity();
        
        $fileId = (int) Yii::app()->request->getParam('attachmentId');
        $file   = MyDocument::model()->findByPk($fileId);
        
        $this->sendJSON(array(
            'result' => (int)MyDocumentsService::makeDocumentVisibleInSimulation($simulation, $file),
            'file'   => [
                    'id'   => $file->id,
                    'name' => $file->fileName,
                    'mime' => $file->template->getMimeType(),
                ] 
        ));
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

        //exit;

        $result = array(
            'result'           => 1,
            'filedId'          => $file->id,
            'excelDocumentUrl' => $zoho->getUrl(),
            'errors'           => $errors,
            'responses'        => $responses,
            'fn1'              => $file->template->srcFile,
            'fn2'              => $file->fileName,
        );
        $this->sendJSON(
            $result
        );
    }
}
