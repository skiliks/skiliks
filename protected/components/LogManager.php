<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivan
 * Date: 1/14/13
 * Time: 11:55 AM
 * To change this template use File | Settings | File Templates.
 */
class LogManager
{

    const ACTION_CLOSE = "0"; //Закрытие окна

    const ACTION_OPEN = "1"; //Открытие окна

    const ACTION_SWITCH = "2"; //Переход в рамках окна

    const ACTION_ACTIVATED = "activated"; //Активация окна

    const ACTION_DEACTIVATED = "deactivated"; //Деактивация окна

    public function setUniversalLog($simId, $logs) {

        if (!is_array($logs)) return false;
        foreach( $logs as $log ) {

            if( self::ACTION_OPEN == (string)$log[2] || self::ACTION_ACTIVATED == (string)$log[2]) {
                if (UniversalLog::model()->countByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId))) {
                    throw(new CException('Previous window is still activated'));
                }
                $universal_log = new UniversalLog();
                $universal_log->sim_id = $simId;
                $universal_log->window_id = $log[1];
                $universal_log->mail_id = empty($log[4]['mailId']) ? NULL : $log[4]['mailId'];
                $universal_log->file_id = empty($log[4]['fileId'])?null:$log[4]['fileId'];
                $universal_log->dialog_id = empty($log[4]['dialogId'])?null:$log[4]['dialogId'];
                $universal_log->start_time  = date("H:i:s", $log[3]);
                $universal_log->save();
                continue;

            } elseif( self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2] ) {
                $universal_logs = UniversalLog::model()->findAllByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId));
                if (0 === count($universal_logs)) {
                    throw(new CException('No active windows. Achtung!'.$simId));
                }
                if (1 < count($universal_logs)) {
                    throw(new CException('Two or more active windows at one time. Achtung!'));
                }
                foreach ($universal_logs as $universal_log) {
                    if(!empty($log['lastDialogId'])){
                        $dialog = Replica::model()->findByAttributes(['id' => $log['lastDialogId'], 'is_final_replica' => 1]);
                    }
                    $universal_log->last_dialog_id = (empty($dialog)) ? null : $dialog->excel_id;
                    $universal_log->end_time = date("H:i:s", $log[3]);
                    $universal_log->save();
                }
            } elseif (self::ACTION_SWITCH == (string)$log[2]) {

                continue;

            } else {

                throw new CException("Unknown action: " . $log[2]);//TODO:Описание доделать
            }
        }

        return true;

    }

}
