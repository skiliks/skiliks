<?php



/**
 * Подготавливает отчет по логированию
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanLogReportController extends AjaxController{
    
    /**
     * Вывод отчета
     */
    public function actionDraw() {
        $sql = "SELECT 
                    d.sim_id,
                    d.day,
                    d.snapshot_time,
                    t.code,
                    t.title,
                    t.category,
                    d.date,
                    0 as is_done,
                    d.todo_count
                FROM day_plan_log as d
                left join tasks as t on (t.id = d.task_id)";
        
        $cmd = Yii::app()->db->createCommand($sql);
        
        
 
        $csv = new ECSVExport($cmd, true, true, ';');
        $csv->setHeaders(array(
            'sim_id'        => Strings::toWin('id_симуляции'), 
            'day'           => Strings::toWin('Графа плана'),
            'snapshot_time' => Strings::toWin('Время логирования состояния плана'),
            'code'          => Strings::toWin('Код задачи'),                
            'title'         => Strings::toWin('Наименование задачи'),
            'category'      => Strings::toWin('Категория задачи'),
            'date'          => Strings::toWin('Время, на которое стоит в плане'),
            'is_done'       => Strings::toWin('Сделана ли задача'),
            'todo_count'    => Strings::toWin('Кол-во задач в "Сделать"')             
        ));
        $csv->setCallback(function($row){
            switch ($row['day']) {
                case 1:
                    $row['day'] = 'today';    
                    $row['date'] = DateHelper::timestampTimeToString($row['date']);
                    break;

                case 2:
                    $row['day'] = 'tomorrow';    
                    $row['date'] = DateHelper::timestampTimeToString($row['date']);
                    break;
                
                case 3:
                    $row['day'] = 'after vacation';    
                    $row['date'] = 'any';
                    break;
            }
            
            //$row['snapshot_time'] = DateHelper::toString($row['snapshot_time']);
            if ($row['snapshot_time'] == 1) $row['snapshot_time'] = '11:00';
            else $row['snapshot_time'] = 'end';
            
            
            $row['title'] = Strings::toWin($row['title']);
            
            $row['is_done'] = 'no';
            
            return $row;
        });
        $content = $csv->toCSV(); // returns string by default
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);
        exit();    
    }
}


