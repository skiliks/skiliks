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

        $id = Yii::app()->request->getParam('id', NULL);
        $file = MyDocumentsModel::model()->findByAttributes(['sim_id' => $simulation->id, 'id' => $id]);
        assert($file);
        $this->sendJSON(
            ExcelFactory::getDocument()
                ->loadByFile($simulation->id, $file)
                ->populateFrontendResult($simulation, $file)
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



