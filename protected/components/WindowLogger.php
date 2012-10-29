<?php



/**
 * Логирование открытых окон.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class WindowLogger {
    
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
     * Логирует активные окна
     * @param int $simId
     * @param array $logs
     * @param int $activeWindow 
     */
    public static function log($simId, $logs, $activeWindow) {
        Logger::debug("log : ".var_export($logs, true));
        
        foreach($logs as $index=>$log) {
            $screenCode         = (int)$log[0];
            $screenActionsCode  = (int)$log[1];
            $time               = $log[2]; //DateHelper::timeToTimstamp($log[2]);
            
            if ($screenActionsCode == 1) { // open
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
    }
}

?>
