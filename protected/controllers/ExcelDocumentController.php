<?php
/**
 * Контроллер документа Excel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentController extends AjaxController
{
    /**
     * New code!
     * @autor Slavka
     * @return 
     */
    public function actionGet() 
    {
        $simulation = $this->getSimulationEntity();
        
        $fileId = (int)Yii::app()->request->getParam('fileId', NULL);
        
        $this->sendJSON(
           ExcelFactory::getDocument()
           ->loadByFile($simulation->id, $fileId)
           ->populateFrontendResult($simulation, $fileId)
       );
    }

    /**
     * New code!
     */
    public function actionGetExcelID() 
    {
        $simulation = $this->getSimulationEntity();
        
        $this->sendJSON(
            MyDocumentsService::checkDocumentTime(
                $simulation, 
                Yii::app()->request->getParam('fileId', NULL)
            )
        );
    }
}



