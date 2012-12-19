<?php
class ZohoController extends CController
{
    public function actionSaveExcel()
    {
        $status = ZohoDocuments::saveFile(
            Yii::app()->getRequest()->getParam('id'), 
            $_FILES['content']['tmp_name'], 
            'xls'
        );
        
        $this->renderText('RESPONSE: '.$status);
    }
}


