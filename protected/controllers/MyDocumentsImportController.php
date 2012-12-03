<?php


/**
 * Контроллер импорта документов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsImportController extends AjaxController{
    
    /**
     * Импорт документов
     */
    public function actionImport() {
        $fileName = 'media/xls/documents.csv';
        
        
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) continue;
            
            if ($index > 29) {
                echo('all done'); die();
            }
            
            // Определим код документа
            $code       = $row[0]; // A
            // Определим тип документа
            $type       = $row[1]; // B
            // Имя файла в системе
            $fileName   = iconv("Windows-1251", "UTF-8", $row[2]); // C
            // Исходный файл
            $srcFile    = iconv("Windows-1251", "UTF-8", $row[3]); // D
            // Расширение файла
            $format     = $row[4]; // E
            
            //if ($type == '-') continue;
            
            $document = MyDocumentsTemplateModel::model()->byCode($code)->find();
            if (!$document) {
                $document = new MyDocumentsTemplateModel();
                $document->code         = $code;
            }
            
            $document->fileName     = $fileName.'.'.$format;
            $document->srcFile      = $srcFile;
            $document->format       = $format;
            $document->type         = $type;
            $document->save();
        }
        fclose($handle);
        echo("Done");
    }
}


