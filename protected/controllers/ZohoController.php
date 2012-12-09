<?php
class ZohoController extends CController
{
    public function actionSaveExcel()
    {
        header('Content-type: text/html; charset=utf-8');
        
        /*$path = explode('-', $returnedId);
        
        if (2 !== count($path)) {
            echo 'RESPONSE: Wrong document id!';
            die;
        }
        
        $zohoDocument = new ZohoDocuments($simId, $fileId, $this->file->getRealFileName());
        $this->zohoDocument[$simId][$fileId]->sendDocumentToZoho();
        
        $name = explode('-',iconv('UTF-8', 'ASCII', $_FILES['content']['name']));
        
        $simId = $name[0];
        $documentID = $name[1];
        
        unset($name[0], $name[1]);
        
        $realFileName = implode('-', $name);
        
        $f = fopen('documents/excel/log.txt', 'w+');
        fwrite($f, "--- \n");
        //fwrite($f, $_SERVER[REQUEST_URI]);
        $r = Yii::app()->getRequest()->getParam('id');
        fwrite($f, $r);
        
        $pathToUserFile = sprintf(
            'documents/excel/%s/%s/%s',
            $simId,
            $documentID,
            StringTools::CyToEn($realFileName)
        );
        //fwrite($f,$pathToUserFile);
        
         fclose($f);

        move_uploaded_file($_FILES['content']['tmp_name'], $pathToUserFile);*/
        
        $status = ZohoDocuments::saveFile(
            Yii::app()->getRequest()->getParam('id'), 
            $_FILES['content']['tmp_name'], 
            'xls'
        );
        
        echo 'RESPONSE: '.$status;
        die;
    }
}


