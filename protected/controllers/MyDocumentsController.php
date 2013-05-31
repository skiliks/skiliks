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
     * Sheet saving
     */
    public function actionSaveSheet($id)
    {
        $simulation = $this->getSimulationEntity();
        $clientModel = CJSON::decode(Yii::app()->request->getParam('model'));

        /** @var MyDocument $file */
        $file   = MyDocument::model()->findByPk($id);
        assert($file->simulation->getPrimaryKey() == $simulation->getPrimaryKey());
        $content = $clientModel['content'];
        $name = $clientModel['name'];

        $file->setSheetContent($name, $content);

        $this->sendJSON(array(
            'result' => 1,
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

        $simulation = $this->getSimulationEntity();

        $id = Yii::app()->request->getParam('id', NULL);
        /** @var MyDocument $file */
        $file = MyDocument::model()->findByAttributes(['sim_id' => $simulation->id, 'id' => $id]);
        assert($file);

        $result = array(
            'result' => 1,
            'fileId' => $file->id,
            'data'   => $file->getSheetList()
        );
        $this->sendJSON(
            $result
        );
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

        $this->sendJSON([
            'status' => (int)($zoho->checkIsUserFileExists() && $file->is_was_saved),
            'IsUserFileExists' => $zoho->checkIsUserFileExists(),
            'is_was_saved' => $file->is_was_saved,
            'id' => $id,
            'id_2' => $file->id,
        ]);
    }
}
