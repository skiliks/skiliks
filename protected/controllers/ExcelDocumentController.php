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



