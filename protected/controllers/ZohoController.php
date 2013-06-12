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
        $f =  new Feedback();
        $f->theme = 'ID';
        $f->message = Yii::app()->getRequest()->getParam('id');
        $f->save(false);

        $status = ZohoDocuments::saveFile(
            Yii::app()->getRequest()->getParam('id'), 
            $_POST['content_path'],
            'xls'
        );
        
        $this->_sendResponse(200, 'RESPONSE: '.$status);
    } 
}


