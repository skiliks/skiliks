<?php
/**
 * 
 */
class ZohoController extends AjaxController
{
    /**
     * 
     */
    public function actionSaveExcel()
    {
        $status = ZohoDocuments::saveFile(
            Yii::app()->getRequest()->getParam('id'), 
            $_FILES['content']['tmp_name'], 
            'xls'
        );
        
        $this->_sendResponse(200, 'RESPONSE: '.$status);
    } 
}


