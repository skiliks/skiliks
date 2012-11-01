<?php



/**
 * Логирование открытых окон.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class WindowLogger {
    
    const mainScreen = 1;
    
    public static $screens = array(
        1 => 'mainScreen', //0
        2 => 'dialog', //1,
        3 => 'dayPlan', //:2,
        4 => 'excel', //:3,
        5 => 'mailEmulator', //:4,
        6 => 'documents', //:5,
        7 => 'viewer', //:6,
        8 => 'phone' //:7
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
            $screenActionsCode  = (int)$log[1];
            $time               = $log[2]; //DateHelper::timeToTimstamp($log[2]);
            
            if ($screenActionsCode == 1) { // open
                // учтем еще момен - а не было ли предыдущее окно у нас mainScreen
                if ($index == 0) {
                    $model = self::getNearestNotClosedWindow($simId, self::mainScreen);
                    if ($model) {
                        // закроем главное окно
                        $model->timeEnd = $time;
                        $model->save();
                    }
                }
                
                $subWindow = 0;
                // проверим а нет ли под нами незакрытого окна - если есть то оно станет подокном
                $model = WindowLogModel::model()->bySimulation($simId)->nearest()->notActiveWindow($screenCode)->isNotClosed()->find();
                if ($model) {
                    // определим подокно
                    $subWindow = $model->activeWindow;
                }
                
                $model = new WindowLogModel();
                $model->sim_id          = $simId;
                $model->activeWindow    = $screenCode;
                $model->activeSubWindow = $subWindow;
                $model->timeStart       = $time;
                $model->insert();
                
                
            }
            
            if ($screenActionsCode == 0) { // close
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
        
        $model = new WindowLogModel();
        $model->sim_id          = $simId;
        $model->activeWindow    = $activeWindow;
        $model->activeSubWindow = 0; //$subWindow;
        $model->timeStart       = $time;
        $model->insert();
    }
}

?>
