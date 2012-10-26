<?php



/**
 * Контроллер менеджера логирования
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class LogManagerController extends AjaxController{
    
    protected function _convert($str) {
        return iconv("UTF-8","Windows-1251", $str);
    }
    
    public function actionGetWindows() {
        $sql = "SELECT 
                    s.user_id,
                    u.email,
                    s.start,
                    s.end,                
                    s.id,
                    w.activeWindow,
                    w.activeSubWindow,
                    w.timeStart,
                    w.timeEnd                
                FROM window_log as w
                left join simulations as s on (s.id = w.sim_id)
                left join users as u on (u.id=s.user_id)";
        
        $cmd = Yii::app()->db->createCommand($sql);
        
        
 
        $csv = new ECSVExport($cmd, true, true, ';');
        $csv->setHeaders(array(
            'user_id'           => $this->_convert('id_пользователя'), 
            'email'             => $this->_convert('email'),
            'start'             => $this->_convert('дата старта симуляции'),
            'end'               => $this->_convert('дата окончания симуляции'),                
            'id'                => $this->_convert('id_симуляции'),
            'activeWindow'      => $this->_convert('Активное окно'),
            'activeSubWindow'   => $this->_convert('Активное подокно'),
            'timeStart'         => $this->_convert('Игровое время - start'),
            'timeEnd'           => $this->_convert('Игровое время - end')                
        ));
        $csv->setCallback(function($row){
            $row['start'] = DateHelper::toString($row['start']);
            if ($row['end'] > 0)
                $row['end'] = DateHelper::toString($row['end']);
            
            $row['timeStart'] = DateHelper::timestampTimeToString($row['timeStart']);
            $row['timeEnd'] = DateHelper::timestampTimeToString($row['timeEnd']);
            
            if (isset(WindowLogger::$screens[$row['activeWindow']]))
                $row['activeWindow'] = WindowLogger::$screens[$row['activeWindow']];
            
            if (isset(WindowLogger::$screens[$row['activeSubWindow']]))
                $row['activeSubWindow'] = WindowLogger::$screens[$row['activeSubWindow']];
            
            //$screens
            return $row;
        });
        $content = $csv->toCSV(); // returns string by default
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);
        exit();
    }
}

?>
