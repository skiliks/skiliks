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

        $fileName = Yii::app()->request->getParam('fileName', NULL);
        $fileName = preg_replace('/.*\//', '', $fileName);
        $file_template = MyDocumentsTemplateModel::model()->findByAttributes(['srcFile' => $fileName]);
        $file = MyDocumentsModel::model()->findByAttributes(['template_id' => $file_template->id, 'sim_id' => $simulation->id]);
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



