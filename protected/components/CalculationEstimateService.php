<?php


/**
 * Сервис расчет оценки
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CalculationEstimateService {
    
    
    /**
     *
     * @param int $dialogId идентификатор диалога
     * @param int $simId идентификатор симуляции
     */
    public static function calculate($dialogId, $simId) {
        
        Logger::debug("calculate estimate for dialog : {$dialogId}");
        // Case 1
        $duration = 0;
        $dialogsDurations = SimulationsDialogsDurations::model()->bySimulation($simId)->find();
        if ($dialogsDurations) {
            $duration = $dialogsDurations->duration;
        }
        Logger::debug("found duration for simulation : {$duration}");
        
        // получить duration
        $dialog = Dialogs::model()->byId($dialogId)->find();
        if (!$dialog) throw new Exception ("Cant find dialog for {$dialogId}");
        $duration += (int)$dialog->duration;
        
        Logger::debug("found duration for dialog {$dialogId} as {$dialog->duration}");
        Logger::debug("duration incremented to : {$duration}");
        
        $dialogs = array();
        $dialogs[] = $dialogId;
        
        
        // 2) к записи, если таковая существует, которая имеет code = code записи, полученной с фронта,  
        // step_number = (step_number записи, полученной с фронта  + 1), replica_number=0
        $dialogCollection = Dialogs::model()->findByAttributes(array(
            'code' => $dialog->code,
            'step_number' => $dialog->step_number,  //+1
            'replica_number' => 0
        ));
        
        Logger::debug("loaded collection for diealog by code : {$dialog->code}");
        if (is_array($dialogCollection)) {
            foreach($dialogCollection as $curDialog) {
                $duration += (int)$curDialog->duration;
                Logger::debug("child dialog ({$curDialog->id}) duration is  : {$curDialog->duration}");
                Logger::debug("total duration incremented to : {$duration}");

                $dialogs[] = $curDialog->id;
            }
        }
        
       
        
        // и добавить в simulations_dialogs_durations
        if ($dialogsDurations) {
            $dialogsDurations->duration = (int)$duration;
            $dialogsDurations->save();
        }
        else {
            $dialogsDurations = new SimulationsDialogsDurations();
            $dialogsDurations->sim_id = $simId;
            $dialogsDurations->duration = (int)$duration;
            $dialogsDurations->insert();
        }
        
        $dialogsStr = implode(',', $dialogs);
        
        // Case 2
        $sql = "select 
                    cp.point_id,
                    cp.add_value,
                    cpt.scale    
                from characters_points as cp
                left join characters_points_titles as cpt on (cpt.id = cp.point_id)
                where cp.dialog_id in ({$dialogsStr})";
                
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $data = array();
        foreach($dataReader as $row) { 
            if (!isset($data[$row['point_id']])) {
                $data[$row['point_id']] = array(
                    'value' => 0, 'count' => 0
                );
            }
            // пробег по каждому поинту
            // value+= add_value*scale
            // count++
            $data[$row['point_id']]['value'] +=  $row['add_value']*$row['scale'];
            $data[$row['point_id']]['count']++;
        }        
        
        // сохраняем данные в simulations_dialogs_points
        foreach($data as $pointId=>$item) {
            $dialogsPoints = SimulationsDialogsPoints::model()->bySimulationAndPoint($simId, $pointId);
            if ($dialogsPoints) {
                $dialogsPoints->value += $item['value'];
                $dialogsPoints->count += $item['count'];
                $dialogsPoints->save();
            }
            else {
                $dialogsPoints = new SimulationsDialogsPoints();
                $dialogsPoints->sim_id = $simId;
                $dialogsPoints->point_id = $pointId;
                $dialogsPoints->value = $item['value'];
                $dialogsPoints->count = $item['count'];
                $dialogsPoints->insert();
            }
        }
        
        return true;
    }
}

?>
