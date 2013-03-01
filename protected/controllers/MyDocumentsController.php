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
        $simulation = $this->getSimulationEntity();

        $id = Yii::app()->request->getParam('id', NULL);
        $file = MyDocument::model()->findByAttributes(['sim_id' => $simulation->id, 'id' => $id]);
        assert($file);
        $zoho = new ZohoDocuments($simulation->primaryKey, $file->primaryKey, $file->template->srcFile);
        $zoho->sendDocumentToZoho();
        $result = array(
            'result'           => 1,
            'filedId'          => $file->id,
            'excelDocumentUrl' => $zoho->getUrl(),
        );
        $this->sendJSON(
            $result
        );
    }
}
