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
    
    public function actionGetWindowsold() {
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
            
            $row['timeStart'] = DateHelper::timestampFullTimeToString($row['timeStart']);
            $row['timeEnd'] = DateHelper::timestampFullTimeToString($row['timeEnd']);
            
            if (isset(WindowLogger::$screens[$row['activeWindow']]))
                $row['activeWindow'] = WindowLogger::$screens[$row['activeWindow']];
            
            if (isset(WindowLogger::$subScreens[$row['activeSubWindow']]))
                $row['activeSubWindow'] = WindowLogger::$subScreens[$row['activeSubWindow']];
            
            return $row;
        });
        $content = $csv->toCSV(); 
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);
        exit();
    }
    
    //ф-ция обработки логов
    public function parseSkiliksLogsold($logs, $start, $end)
    {
        $logTemp = array();
        $newLogs = array();
        $cashedKey = -1;
        //делаем обратную сортировку по ключам (определяем приоритет)
        krsort($logs);

        for($i=$start;$i<($end+1);$i++){
            //надо определить новую запись
            foreach ($logs as $key => $value) {
                if(isset($logs[$key]) && $logs[$key]['timeStart']<=$i && $i<$logs[$key]['timeEnd']){
                    //ага, нашли элемент, и он не равен нашему, ранее закешированному
                    if($key != $cashedKey){
                        //надо дополнить и созранить темповый лог 
                        if(count($logTemp)>0){
                            $logTemp['timeEnd'] = $i;
                            $newLogs[$logTemp['timeStart']] = $logTemp;

                            //удаляем ненужный элемент из логов, если он уже просрочен
                            if($logs[$cashedKey]['timeEnd']<$i){
                                unset($logs[$cashedKey]);
                            }
                        }

                        /*echo $i.') key='.$key.'<br>';*/

                        //создаем новый темп лог
                        $cashedKey = $key;
                        $logTemp = $logs[$key];
                        $logTemp['timeStart'] = $i;
                        $logTemp['timeEnd'] = 0;
                    }
                    break;
                }
            }




        }
        //а вдруг мы что-то не дозакрыли
        if(count($logTemp)>0){
            $logTemp['timeEnd'] = ($i-1);
            $newLogs[$logTemp['timeStart']] = $logTemp;
        }

        return $newLogs;
    }
    
    //ф-ция обработки логов
    public function parseSkiliksLogs($logs, $logMainScreen)
    {
        $newArray = array($logMainScreen);
        ksort($logs);

        //прогоняем с каждым элементом массива
        foreach ($logs as $log){
            $newArray = $this->skiliksLogsMerge($newArray, $log);
        }

        //последующая обработка и сортировка массива
        $output = array();
        foreach ($newArray as $value){
            if (isset($value['timeStart']))
            $output[$value['timeStart']] = $value;
        }

        ksort($output);

        return $output;
    }

    public function skiliksLogsMerge($existing, $new)
    {   
        $leftStemp = -1;
        $leftK = -1;
        $left = array();

        $rightEtemp = 10000000000;
        $rightK = -1;
        $right = array();
        foreach ($existing as $key => $value) {
            if (!isset($value['timeStart'])) {
                continue;
            }
            
            //определяем ближайшую точку слева
            if( ($leftStemp < $value['timeStart']) && ($value['timeStart'] < $new['timeStart']) ){
                $leftStemp = $value['timeStart'];
                $leftK = $key;
                $left = $value;
            }
            //определяем ближайшую точку справа
            if( ($rightEtemp > $value['timeEnd']) && ($new['timeEnd'] < $value['timeEnd'] ) ){
                $rightEtemp = $value['timeEnd'];
                $rightK = $key;
                $right = $value;
            }
        }


        if($leftK == $rightK || $leftK == -1 || $rightK == -1){
                //это если точки совпадают точки, т.е. новый лог внутри другого
                if($leftK != -1){
                    $new_left = $left;
                    $new_left['timeEnd'] = $new['timeStart'];
                }

                if($rightK != -1){
                    $new_right = $right;
                    $new_right['timeStart'] = $new['timeEnd'];
                }

                unset ($existing[$leftK]);
                if($leftK != -1){
                    $existing[] = $new_left;
                }
                $existing[] = $new;
                if($rightK != -1){
                    $existing[] = $new_right;
                }
            }else{
                //точки разные, делаем смещение и вставляем новый лог
                $existing[$leftK]['timeEnd'] = $new['timeStart'];
                $existing[$rightK]['timeStart'] = $new['timeEnd'];
                $existing[] = $new;
            }

        return $existing;
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
                left join users as u on (u.id=s.user_id) order by id";
        
        $cmd = Yii::app()->db->createCommand($sql);
        $dataReader = $cmd->query();
        $data = array();
        $dataMain = array();
        
        $simId = 0;
        $simStart = 0;
        $simEnd = 0;
        
        $fp = fopen('media/test.csv', 'w');
        $fields = array(
            'user_id'           => $this->_convert('id_пользователя'), 
            'email'             => $this->_convert('email'),
            'start'             => $this->_convert('дата старта симуляции'),
            'end'               => $this->_convert('дата окончания симуляции'),                
            'id'                => $this->_convert('id_симуляции'),
            'activeWindow'      => $this->_convert('Активное окно'),
            'activeSubWindow'   => $this->_convert('Активное подокно'),
            'timeStart'         => $this->_convert('Игровое время - start'),
            'timeEnd'           => $this->_convert('Игровое время - end')                
        );
        fputcsv($fp, $fields, ';');
        
        foreach($dataReader as $row) { 
            /*echo("process row");
            var_dump($row);*/
            
            $activeSimId = $row['id'];
            $timeStart = $row['timeStart'];
            $timeEnd = $row['timeEnd'];
            
            if ($simId == 0) $simId = $activeSimId;
            
            if (isset(WindowLogger::$screens[$row['activeWindow']]))
                $row['name'] = WindowLogger::$screens[$row['activeWindow']];
            
            if ((int)$row['activeWindow'] != 1) {
                $data[$row['timeStart']]=$row;  
            }
            else {
                $dataMain = $row;
            }
            
            if ($activeSimId != $simId ) {
                // тут делаем выгрузку и обработку
                
                if (count($data) > 0) {
                    /*echo("<hr>");
                    echo("data");
                    var_dump($data);
                    echo("dataMain");
                    var_dump($dataMain);*/
                    
                    $data = $this->parseSkiliksLogs($data, $dataMain);
                    
                    foreach ($data as $fields) {
                        $fields['timeStart'] = DateHelper::timestampFullTimeToString($fields['timeStart']);
                        $fields['timeEnd'] = DateHelper::timestampFullTimeToString($fields['timeEnd']);
                        $fields['start'] = DateHelper::toString($fields['start']);
                        $fields['end'] = DateHelper::toString($fields['end']);
                        if (isset(WindowLogger::$screens[$fields['activeWindow']]))
                            $fields['activeWindow'] = WindowLogger::$screens[$fields['activeWindow']];
                        if (isset(WindowLogger::$subScreens[$fields['activeSubWindow']]))
                            $fields['activeSubWindow'] = WindowLogger::$subScreens[$fields['activeSubWindow']];
                        unset($fields['name']);
                        fputcsv($fp, $fields, ';');
                    }
                }
                //$data = $this->parseSkiliksLogs($data, $simStart, $simEnd);
                
                
                
                $data = array();
                $dataMain = array();
                $simId = $activeSimId;
                
                if ($row['activeWindow'] != 1) {
                    $data[$row['timeStart']]=$row;  
                }
                else {
                    $dataMain = $row;
                }
            }
            
            
        }
        
        // учтем последний элемент
        if (count($data) > 0) {
                    /*echo("<hr>");
                    echo("data");
                    var_dump($data);
                    echo("dataMain");
                    var_dump($dataMain);*/
                    
                    $data = $this->parseSkiliksLogs($data, $dataMain);
                    
                    foreach ($data as $fields) {
                        $fields['timeStart'] = DateHelper::timestampFullTimeToString($fields['timeStart']);
                        $fields['timeEnd'] = DateHelper::timestampFullTimeToString($fields['timeEnd']);
                        $fields['start'] = DateHelper::toString($fields['start']);
                        $fields['end'] = DateHelper::toString($fields['end']);
                        $fields['activeWindow'] = WindowLogger::$screens[$fields['activeWindow']];
                        $fields['activeSubWindow'] = WindowLogger::$subScreens[$fields['activeSubWindow']];
                        unset($fields['name']);
                        fputcsv($fp, $fields, ';');
                    }
                }
        
        fclose($fp);
        
        $content = file_get_contents('media/test.csv');
        Yii::app()->getRequest()->sendFile('media/test.csv', $content, "text/csv", false);
        exit();
    }
}

?>
