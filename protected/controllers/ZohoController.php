<?php
class ZohoController extends CController
{
    public function actionSaveExcel()
    {
        header('Content-type: text/html; charset=utf-8');
        
        $name = explode('-',iconv('UTF-8', 'Windows-1250', $_FILES['content']['name']));
        
        $simId = $name[0];
        $documentID = $name[1];
        
        unset($name[0], $name[1]);
        
        $realFileName = implode('-', $name);
        
        $f = fopen('documents/excel/log.txt', 'w');
        fwrite($f, "--- \n");
        fwrite($f, mb_detect_encoding($realFileName, mb_detect_order(), true)."\n");
        
        $realFileName = iconv(mb_detect_encoding($realFileName, mb_detect_order(), true), "UTF-8//IGNORE", $realFileName);
        fwrite($f, mb_detect_encoding($realFileName, mb_detect_order(), true)."\n");
        
        $realFileName = iconv('ASCII' , "UTF-8//IGNORE", $realFileName);
        fwrite($f, mb_detect_encoding($realFileName, mb_detect_order(), true)."\n");
        
        $realFileName = utf8_encode($realFileName);
        fwrite($f, mb_detect_encoding($realFileName, mb_detect_order(), true)."\n");
        
        $realFileName = iconv("ASCII", "UTF-8", $realFileName);
        fwrite($f, mb_detect_encoding($realFileName, mb_detect_order(), true)."\n");
        
        fclose($f);
        
        $pathToUserFile = sprintf(
            'documents/excel/%s/%s/%s',
            $simId,
            $documentID,
            $realFileName
        );

        move_uploaded_file($_FILES['content']['tmp_name'], $pathToUserFile);
        
        echo 'RESPONSE: Saved.';
        die;
    }
}


