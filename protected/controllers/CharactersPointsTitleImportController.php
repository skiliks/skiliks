<?php



/**
 * Импорт формы 1 лайт
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPointsTitleImportController extends AjaxController{
    
    public function actionImport() {
        $fileName = 'media/xls/points.csv';
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index == 1) {
                continue;
            }
            //var_dump($row);
            $code = $row[0];
            $scale = $row[4];
            $scale = str_replace(',', '.', $scale);
            $name = iconv("Windows-1251", "UTF-8", $row[3]);
            
            echo("$code $scale <br/>");
            
            $model = CharactersPointsTitles::model()->byCode($code)->find();
            if (!$model) {
                $model = new CharactersPointsTitles();
                $model->parent_id = 1;
                $model->code = $code;
                $model->title = $name;
                $model->scale = $scale;
                $model->insert();
                echo($name);
                var_dump($code); 
            }
            else {
                $model->scale = $scale;
                $model->update();
            }
        }
        fclose($handle);
        echo("Done");
    }
}

?>
