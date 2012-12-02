<?php



/**
 * Description of TasksImportController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class TasksImportController extends AjaxController{
    
    public function actionImport() {
        $fileName = 'media/xls/tasks.csv';
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;

        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            
            if ($index == 1) continue;
            
            
            if ($index > 40) {
                echo('all done'); die();
            }
            
            // Код
            $code       = $row[0]; // A
            // Тип старта задачи
            $startType  = $row[1]; // B
            // Список дел в to-do-list
            $name       = iconv("Windows-1251", "UTF-8", $row[2]); // C
            // Жесткая
            $startTime  = $row[3]; // D
            if ($startTime != '') {
                if (strstr($startTime, ':')) {
                    $timeData = explode(':', $startTime);
                    if (count($timeData) > 1) {
                      $startTime = $timeData[0]*60 + $timeData[1];
                    }
                }
            }
            
            // Категория
            $category   = $row[4];  // E
            // Мин.
            $duration   = $row[5];  // F
            
            $task = Tasks::model()->byCode($code)->find();
            if (!$task) {
                $task = new Tasks();
                $task->code = $code;
            }
            
            $task->title = $name;
            $task->start_time = $startTime;
            $task->duration = $duration;
            if ($startTime > 0) $task->type = 2;
            else $task->type = 1;
            $task->start_type = $startType;
            $task->category = $category;
            $task->save();
        }
        fclose($handle);
        echo("Done");
    }    
}


