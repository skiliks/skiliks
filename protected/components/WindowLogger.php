<?php



/**
 * Логирование открытых окон.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class WindowLogger {
    
    const mainScreen = 1;
    
    const ACTION_CLOSE = 0;
    
    const ACTION_OPEN = 1;
    
    const ACTION_SWITCH = 2;
    
    const ACTION_MAIL_PREVIEW = 102;
    
    const ACTION_MAIL_NEW = 103;
    

    public static $screens = array(
        1 => 'main screen', 
        
        3 => 'plan', 
        4 => 'excel', 
        6 => 'documents', 
        7 => 'documents files', 
        10 => 'mail',
        11 => 'mail main',
        12 => 'mail preview',
        13 => 'mail new',
        14 => 'mail plan',
        20 => 'phone',
        21 => 'phone main', //'phoneHistory',
        22 => 'phoneContacts',
        
        23 => 'phone talk',
        24 => 'phone call',
        25 => 'phone main',
        
        30 => 'visitor', 
        
        31 => 'visitor entrance',
        32 => 'visitor talk'
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
    public static function getNearestNotClosedWindow($simId, $screenCode) {
        return WindowLogModel::model()->bySimulation($simId)->nearest()->byActiveWindow($screenCode)->isNotClosed()->find();
    }
    
    public static function hasSameNotClosedWindow($simId, $screenCode, $subScreenCode) {
        return (bool)WindowLogModel::model()->bySimulation($simId)->nearest()
                ->byActiveWindow($screenCode)->byActiveSubWindow($subScreenCode)->isNotClosed()->find();
    }
    
    /**
     * Логирует активные окна
     * @param int $simId
     * @param array $logs
     * @param int $activeWindow 
     */
    public static function log($simId, $logs, $activeWindow) {
        Logger::debug("log : ".var_export($logs, true));
        
        if (count($logs) == 0) {
            return false; // нечего логировать
        }
        
        $time = 0;
        foreach($logs as $index=>$log) {
            $screenCode         = (int)$log[0];
            $subScreenCode      = (int)$log[1];
            $screenActionsCode  = (int)$log[2];
            $time               = $log[3]; //DateHelper::timeToTimstamp($log[2]);
            
            /*if ($screenActionsCode > 1) {
                $screenCode = $screenActionsCode;
            }*/
            
            if ($screenActionsCode == self::ACTION_OPEN || $screenActionsCode == self::ACTION_SWITCH) { // open
                // учтем еще момен - а не было ли предыдущее окно у нас mainScreen
                if ($index == 0) {
                    $model = self::getNearestNotClosedWindow($simId, self::mainScreen);
                    if ($model) {
                        // закроем главное окно
                        $model->timeEnd = $time;
                        $model->save();
                    }
                }
                
                
                if ($subScreenCode == 1) {
                    // проверим а нет ли под нами незакрытого окна - если есть то оно станет подокном
                    $model = WindowLogModel::model()->bySimulation($simId)->nearest()->notActiveWindow($screenCode)->isNotClosed()->find();
                    if ($model) {
                        // определим подокно
                        $subScreenCode = $model->activeWindow;
                    }
                }
                
                if ($subScreenCode == 0 && $screenCode > 1) {
                    $subScreenCode = 1; // mainScreen
                }
                
                
                // надо бы закрыть родительское окно
                if ($subScreenCode > 1) {
                    $model = WindowLogModel::model()->bySimulation($simId)->nearest()->notActiveWindow($screenCode)->isNotClosed()->find();
                    if ($model) {
                        $model->timeEnd = $time;
                        $model->save();
                    }
                }
                
                // проверим а не ли у нас уже такой записи
                if (!self::hasSameNotClosedWindow($simId, $screenCode, $subScreenCode)) {
                    $model = new WindowLogModel();
                    $model->sim_id          = $simId;
                    $model->activeWindow    = $screenCode;
                    $model->activeSubWindow = $subScreenCode;
                    $model->timeStart       = $time;
                    $model->insert();
                }
            }
            
            if ($screenActionsCode == self::ACTION_CLOSE) { // close
                // закроем окно
                Logger::debug("close window : $activeWindow");
                $model = WindowLogModel::model()->bySimulation($simId)->byActiveWindow($screenCode)->nearest()->find();
                if ($model) {
                    Logger::debug("find model id : {$model->id}");
                    $model->timeEnd = $time;
                    $model->save();
                }
            }
        }
        
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
        $model = new WindowLogModel();
        $model->sim_id          = $simId;
        $model->activeWindow    = $activeWindow;
        $model->activeSubWindow = 0; //$subWindow;
        $model->timeStart       = $time;
        $model->insert();
    }
}

?>
