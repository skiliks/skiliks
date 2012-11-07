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
        Logger::debug("window log : ".var_export($logs, true));
        
        Logger::debug("sim : $simId");
        
        if (count($logs) == 0) {
            return false; // нечего логировать
        }
        
        $time = 0;
        foreach($logs as $index=>$log) {
            $screenCode         = (int)$log[0];  // активное окно
            $subScreenCode      = (int)$log[1];  // активное подокно
            $screenActionsCode  = (int)$log[2];  // дествие open||close||switch
            $time               = $log[3]; //DateHelper::timeToTimstamp($log[2]);
            
            // исключение - добавлены согласно постановке от Антона
            if ($screenCode == self::plan) $subScreenCode = self::plan;
            if ($screenCode == self::mainScreen) $subScreenCode = self::mainScreen;
            
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
                if (!self::hasSameNotClosedWindow($simId, $screenCode, $subScreenCode)) {
                    $model = new WindowLogModel();
                    $model->sim_id          = $simId;
                    $model->activeWindow    = $screenCode;
                    $model->activeSubWindow = $subScreenCode;
                    $model->timeStart       = $time;
                    $model->insert();
                }
                continue;
            }
            
            if ($screenActionsCode == self::ACTION_SWITCH) {
                // проверим а вдруг у нас уже есть такое окно незакрытое пример - phone phoneMain
                $model = WindowLogModel::model()->bySimulation($simId)->byActiveWindow($screenCode)
                        ->byActiveSubWindow($subScreenCode)->nearest()->isNotClosed()->find();
                if ($model) continue; // у нас есть уже такое окно и оно не закрыто
                
                // найти предыдущее окно
                $model = WindowLogModel::model()->bySimulation($simId)->nearest()->isNotClosed()->find();
                if ($model) {
                    // закроем его
                    $model->timeEnd = $time;
                    $model->save();
                }
                
                // открыть окно
                // проверим а не ли у нас уже такой записи
                if (!self::hasSameNotClosedWindow($simId, $screenCode, $subScreenCode)) {
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
