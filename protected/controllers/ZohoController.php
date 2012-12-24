<?php
class ZohoController extends CController
{
    public function actionSaveExcel()
    {
        Yii::log('Zoho save start. ');
        $status = ZohoDocuments::saveFile(
            Yii::app()->getRequest()->getParam('id'), 
            $_FILES['content']['tmp_name'], 
            'xls'
        );
        
        Yii::log('Zoho save status: '.$status);
        
        $this->_sendResponse(200, 'RESPONSE: '.$status);
    }
}


