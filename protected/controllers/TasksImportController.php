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
            
            $code       = $row[0];
            $startType  = $row[1];
            $name       = iconv("Windows-1251", "UTF-8", $row[2]);
            $startTime  = $row[3];
            if ($startTime != '') {
                if (strstr($startTime, ':')) {
                    $timeData = explode(':', $startTime);
                    if (count($timeData) > 1) {
                      $startTime = $timeData[0]*60 + $timeData[1];
                    }
                }
            }
            
            $category   = $row[4];
            $duration   = $row[5];
            
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

?>
