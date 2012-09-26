<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CharactersController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersController extends AjaxController{
    
    public function actionImport() {
        $fileName = 'media/xls/characters.csv';
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        
        /*
        // Добавим героя
        $code = 0;
        $title = 'герой';
        $fio = 'Клинт Иствуд'; 
        
            $model = new Characters();
            $model->code = $code;
            $model->title = $title;
            $model->fio = $fio;
            $model->insert();*/
        
        
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index == 1) {
                continue;
            }
            //var_dump($row);
            $code = $row[0];
            $title = iconv("Windows-1251", "UTF-8", $row[1]);
            $fio = iconv("Windows-1251", "UTF-8", $row[2]);
            $email = $row[3];
            $skype = $row[4];
            $phone = $row[5];
            
            
            
            $model = Characters::model()->byCode($code)->find();
            if (!$model) {
                $model = new Characters();
                $model->code = $code;
                $model->title = $title;
                $model->fio = $fio;
                $model->email = $email;
                $model->skype = $skype;
                $model->phone = $phone;
                $model->insert();
            }
            else {
                $model->code = $code;
                $model->title = $title;
                $model->fio = $fio;
                $model->email = $email;
                $model->skype = $skype;
                $model->phone = $phone;
                $model->update();
            }
        }
        fclose($handle);
        echo("Done");
    }
}

?>
