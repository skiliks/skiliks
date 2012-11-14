<?php



/**
 * Логирование открытых окон.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class WindowLogger {
    
    const mainScreen = 1;
    
    const plan = 3;
    
    const ACTION_CLOSE = 0;
    
    const ACTION_OPEN = 1;
    
    const ACTION_SWITCH = 2;
    
    const ACTION_MAIL_PREVIEW = 102;
    
    const ACTION_MAIL_NEW = 103;
    

    public static $screens = array(
        1 => 'main screen', 
        3 => 'plan', 
        10 => 'mail',
        20 => 'phone',
        30 => 'visitor', 
        40 => 'documents'
    );
    
    public static $subScreens = array(
        1 => 'main screen',
        3 => 'plan',
        11 => 'mail main',
        12 => 'mail preview',
        13 => 'mail new',
        14 => 'mail plan',
        21 => 'phone main',
        23 => 'phone talk',
        24 => 'phone call',
        31 => 'visitor entrance',
        32 => 'visitor talk',
        41 => 'documents main',
        42 => 'documents files'
    );
    
    
    public $screensActions = array(
        'close', //:0,
        'open' //:1
    );
    
    /**
     * Получить ближайшее не закрытое окно по коду
     * 
     * @param int $simId
     * @param int $screenCode
     * @return WindowLogModel 
     */
    public function getNearestNotClosedWindow($simId, $screenCode) {
        return WindowLogModel::model()->bySimulation($simId)->nearest()->byActiveWindow($screenCode)->isNotClosed()->find();
    }
    
    public function hasSameNotClosedWindow($simId, $screenCode, $subScreenCode) {
        return (bool)WindowLogModel::model()->bySimulation($simId)->nearest()
                ->byActiveWindow($screenCode)->byActiveSubWindow($subScreenCode)->isNotClosed()->find();
    }
    
    protected function _processLogs($simId, $logs) {
        foreach($logs as $index=>$log) {
            $screenCode         = (int)$log[0];  // активное окно
            $subScreenCode      = (int)$log[1];  // активное подокно
            $screenActionsCode  = (int)$log[2];  // дествие open||close
            $time               = $log[3]; 
            
            if ($screenActionsCode == self::ACTION_OPEN) {
                // закрыть предыдущее окно
                // найти уже открытое окно
                /*$model = WindowLogModel::model()->bySimulation($simId)->nearest()->isNotClosed()->find();
                if ($model) {
                    // закроем его
                    $model->timeEnd = $time;
                    $model->save();
                }*/
                
                // открыть окно
                // проверим а не ли у нас уже такой записи
                if (!$this->hasSameNotClosedWindow($simId, $screenCode, $subScreenCode)) {
                    $model = new WindowLogModel();
                    $model->sim_id          = $simId;
                    $model->activeWindow    = $screenCode;
                    $model->activeSubWindow = $subScreenCode;
                    $model->timeStart       = $time;
                    $model->insert();
                }
                continue;
            }
           
            if ($screenActionsCode == self::ACTION_CLOSE) {
                $model = WindowLogModel::model()->bySimulation($simId)->byActiveWindow($screenCode)->nearest()->isNotClosed()->find();
                if ($model) {
                    Logger::debug("find model id : {$model->id}");
                    $model->timeEnd = $time;
                    $model->save();
                    continue;
                }
                /*else {
                    // по какой-то причине у нас нет такого лога - найдем ближайшее незакрытое и закроем его
                    $model = WindowLogModel::model()->bySimulation($simId)->nearest()->isNotClosed()->find();
                    if ($model) {
                        Logger::debug("find model id : {$model->id}");
                        $model->timeEnd = $time;
                        $model->save();
                        continue;
                    }
                }*/
            }
            
        } // of foreach
    }
    
    /**
     * Логирует активные окна
     * @param int $simId
     * @param array $logs
     * @param int $activeWindow 
     * @param int $timeString
     */
    public function log($simId, $logs, $activeWindow, $timeString) {
        Logger::debug("window log : ".var_export($logs, true));
        
        Logger::debug("sim : $simId");
                
        
        $time = 0;
        if (is_array($logs) && count($logs)>0) {
            return $this->_processLogs($simId, $logs);
        }
                
        // учтем activeWindow - это надо для случая когда закрыли окно и активно какое-то окно
        $model = WindowLogModel::model()->bySimulation($simId)->nearest()->find();
        if ($model) {
            Logger::debug("cur : {$model->activeWindow} new : $activeWindow");
            if ($model->activeWindow == $activeWindow) return;
        }    
        
        // учтем что у нас может быть открыто какое-то другое окно в это время
        if ($model && $model->activeWindow != $activeWindow) {
            if ($model->timeEnd == 0) {  // окно еще не закрыто
                // закроем его
                $model->timeEnd = $timeString;
                $model->save();
            }
        }
        
        if ($activeWindow == 0) return; // нечего логировать
        
        // пробуем определить активное подокно
        if ($activeWindow == 1) $subScreenCode = 1;
        /*else {
            // пробуем определить подокно
            // проверим а нет ли под нами незакрытого окна - если есть то оно станет подокном
            $model = WindowLogModel::model()->bySimulation($simId)->nearest()->notActiveWindow($screenCode)->isNotClosed()->find();
            if ($model) {
                // определим подокно
                $subScreenCode = $model->activeWindow;
            }
        }*/
        
        if ($activeWindow == 1) {
        $model = new WindowLogModel();
        $model->sim_id          = $simId;
        $model->activeWindow    = $activeWindow;
        $model->activeSubWindow = $activeWindow;
        $model->timeStart       = $timeString;
        $model->insert();
        }
    }
    
    public function stop($simId, $timeString) {
        $connection=Yii::app()->db;   

        $sql = "update window_log set timeEnd = {$timeString} where sim_id=$simId and timeEnd = 0";
        $command = $connection->createCommand($sql);
        $command->execute();
    }
}

?>
