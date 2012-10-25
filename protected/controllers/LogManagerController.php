<?php



/**
 * Контроллер менеджера логирования
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class LogManagerController extends AjaxController{
    
    public function actionGetWindows() {
        $cmd = Yii::app()->db->createCommand("SELECT * FROM window_log");
 
        $csv = new ECSVExport($cmd, true, true, ';');
        $csv->setCallback(function($row){
            $row['timeStart'] = DateHelper::timestampTimeToString($row['timeStart']);
            $row['timeEnd'] = DateHelper::timestampTimeToString($row['timeEnd']);
            return $row;
        });
        $content = $csv->toCSV(); // returns string by default
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);
        exit();
    }
}

?>
