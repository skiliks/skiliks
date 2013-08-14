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
     * Sheet saving
     */
    public function actionSaveSheet($id)
    {
        $simulation = $this->getSimulationEntity();

        /** @var MyDocument $file */
        $file   = MyDocument::model()->findByPk($id);
        assert($file->simulation->getPrimaryKey() == $simulation->getPrimaryKey());

        $content = Yii::app()->request->getParam('model-content');
        $name    = Yii::app()->request->getParam('model-name');

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

}
