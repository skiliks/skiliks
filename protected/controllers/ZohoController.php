<?php
class ZohoController extends CController
{
    public function actionSaveExcel()
    {
        header('Content-type: text/html; charset=utf-8');
        
        $status = ZohoDocuments::saveFile(
            Yii::app()->getRequest()->getParam('id'), 
            $_FILES['content']['tmp_name'], 
            'xls'
        );
        
        echo 'RESPONSE: '.$status;
        die;
    }
}


