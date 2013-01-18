<?php

require_once(__DIR__ . '/../vendors/elFinder/elFinderConnector.class.php');
require_once(__DIR__ . '/../vendors/elFinder/elFinderVolumeDriver.class.php');
require_once(__DIR__ . '/../vendors/elFinder/elFinderVolumeLocalFileSystem.class.php');
require_once(__DIR__ . '/../vendors/elFinder/elFinder.class.php');

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
        
        $this->sendJSON(array(
            'result' => (int)MyDocumentsService::makeDocumentVisibleInSimulation($simulation, $fileId)
        ));
    }
}
