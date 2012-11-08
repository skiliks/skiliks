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
        1 => 'mainScreen',
        3 => 'plan',
        11 => 'mailMain',
        12 => 'mailPreview',
        13 => 'mailNew',
        14 => 'mailPlan',
        21 => 'phoneMain',
        23 => 'phoneTalk',
        24 => 'phoneCall',
        31 => 'visitorEntrance',
        32 => 'visitorTalk',
        41 => 'documents',
        42 => 'documentsFiles'
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
            $screenActionsCode  = (int)$log[2];  // дествие open||close||switch
            $time               = $log[3]; //DateHelper::timeToTimstamp($log[2]);
            
            // исключение - добавлены согласно постановке от Антона
            /*if ($screenCode == self::plan) $subScreenCode = self::plan;
            if ($screenCode == self::mainScreen) $subScreenCode = self::mainScreen;*/
            
            if ($screenActionsCode == self::ACTION_OPEN) {
                // закрыть предыдущее окно
                // найти уже открытое окно
                $model = WindowLogModel::model()->bySimulation($simId)->nearest()->isNotClosed()->find();
                if ($model) {
                    // закроем его
                    $model->timeEnd = $time;
                    $model->save();
                }
                
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
                else {
                    // по какой-то причине у нас нет такого лога - найдем ближайшее незакрытое и закроем его
                    $model = WindowLogModel::model()->bySimulation($simId)->nearest()->isNotClosed()->find();
                    if ($model) {
                        Logger::debug("find model id : {$model->id}");
                        $model->timeEnd = $time;
                        $model->save();
                        continue;
                    }
                }
            }
            
        } // of foreach
    }
    
    /**
     * Логирует активные окна
     * @param int $simId
     * @param array $logs
     * @param int $activeWindow 
     */
    public function log($simId, $logs, $activeWindow) {
        Logger::debug("window log : ".var_export($logs, true));
        
        Logger::debug("sim : $simId");
                
        
        $time = 0;
        if (count($logs)>0) {
            return $this->_processLogs($simId, $logs);
        }
        return;
        
        
        // учтем activeWindow - это надо для случая когда закрыли окно и активно какое-то окно
        $model = WindowLogModel::model()->bySimulation($simId)->nearest()->find();
        if ($model && $model->activeWindow == $activeWindow) {
            return;
        }    
        
        // учтем что у нас может быть открыто какое-то другое окно в это время
        if ($model && $model->activeWindow != $activeWindow) {
            if ($model->timeEnd == 0) {  // окно еще не закрыто
                return; // нельзя логировать mainScreen
            }
        }
        
        if ($activeWindow == 0) return;
        
        // пробуем определить активное подокно
        if ($activeWindow == 1) $subScreenCode = 1;
        else {
            // пробуем определить подокно
            // проверим а нет ли под нами незакрытого окна - если есть то оно станет подокном
            $model = WindowLogModel::model()->bySimulation($simId)->nearest()->notActiveWindow($screenCode)->isNotClosed()->find();
            if ($model) {
                // определим подокно
                $subScreenCode = $model->activeWindow;
            }
        }
        
        $model = new WindowLogModel();
        $model->sim_id          = $simId;
        $model->activeWindow    = $activeWindow;
        $model->activeSubWindow = $subScreenCode;
        $model->timeStart       = $time;
        $model->insert();
    }
}

?>
