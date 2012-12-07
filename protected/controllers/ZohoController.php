<?php
class ZohoController extends CController
{
    public function actionSaveExcel()
    {
        header('Content-type: text/html; charset=utf-8');
        
        $name = explode('-',iconv('UTF-8', 'ASCII', $_FILES['content']['name']));
        
        $simId = $name[0];
        $documentID = $name[1];
        
        unset($name[0], $name[1]);
        
        $realFileName = implode('-', $name);
        
        $pathToUserFile = sprintf(
            'documents/excel/%s/%s/%s',
            $simId,
            $documentID,
            StringTools::CyToEn($realFileName)
        );

        move_uploaded_file($_FILES['content']['tmp_name'], $pathToUserFile);
        
        echo 'RESPONSE: Saved.';
        die;
    }
}


