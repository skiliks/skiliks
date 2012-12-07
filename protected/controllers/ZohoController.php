<?php
class ZohoController extends CController
{
    public function actionSaveExcel()
    {
        // @todo: add nginx rule to make authentification exeption for api/zoho/saveExcel
        $name = explode('-', $_FILES['content']['name']);
        
        $simId = $name[0];
        $documentID = $name[1];
        
        unset($name[0], $name[1]);
        
        $realFileName = implode('-', $name);
        
        $f = fopen('documents/excel/log.txt', 'w');
        fwrite($f, "--- \n");
        fwrite($f, mb_detect_encoding($realFileName, mb_detect_order(), true));
        $realFileName = iconv(mb_detect_encoding($realFileName, mb_detect_order(), true), "UTF-8//IGNORE", $realFileName);
        fwrite($f, mb_detect_encoding($realFileName, mb_detect_order(), true));
        fclose($f);
        
        $pathToUserFile = sprintf(
            'documents/excel/%s/%s/%s',
            $simId,
            $documentID,
            $realFileName
        );

        move_uploaded_file($_FILES['content']['tmp_name'], $pathToUserFile);
        
        echo 'RESPONSE: Файл успешно сохранён.';
        die;
    }
}


