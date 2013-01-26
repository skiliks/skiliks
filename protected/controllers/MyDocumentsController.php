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
        
        $fileId = (int) Yii::app()->request->getParam('attachmentId', null);
        $file   = MyDocumentsModel::model()->byId($fileId)->find();
        
        $this->sendJSON(array(
            'result' => (int)MyDocumentsService::makeDocumentVisibleInSimulation($simulation, $fileId),
            'file'   => [
                    'id'   => (null === $file) ? null : $file->id,
                    'name' => (null === $file) ? null : $file->fileName,
                    'mime' => (null === $file) ? null : $file->template->getMimeType(),
                ] 
        ));
    }
}
