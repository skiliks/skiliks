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
        
        Logger::debug("load collection for code ({$dialog->code}) and step_number ({$dialog->step_number})");
        /*$dialogCollection = Dialogs::model()->findAllByAttributes(array(
            'code' => '"'.$dialog->code.'"',
            'step_number' => $dialog->step_number,  //+1
            'replica_number' => 0
        ));*/
        
        $sql = "select * from dialogs where code='{$dialog->code}' and step_number={$dialog->step_number} and replica_number=0";
        Logger::debug("sql : $sql");
        $command = Yii::app()->db->createCommand($sql);
        $dialogCollection = $command->queryAll();
        
        
        /*$dialogCollection = $command->select('*')->from('dialogs')
                ->where('code=":code and step_number=:step_number and replica_number=0"', 
                        array(':code'=>$dialog->code, 'ste_number'=>$dialog->step_number))->queryAll();*/
        
        
        Logger::debug("loaded collection for diealog by code : {$dialog->code}");
        Logger::debug('collection is : '.var_export($dialogCollection, true));
        //if (is_array($dialogCollection)) {
            foreach($dialogCollection as $curDialog) {
                $duration += (int)$curDialog['duration'];
                Logger::debug("child dialog ({$curDialog['id']}) duration is  : {$curDialog['duration']}");
                Logger::debug("total duration incremented to : {$duration}");

                $dialogs[] = $curDialog['id'];
            }
        //}
        
       
        
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
                
        Logger::debug('case2 sql : '.$sql);        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $data = array();
        foreach($dataReader as $row) { 
            Logger::debug("fill for row ".var_export($row, true));
            
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
        
        Logger::debug("save data :  ".var_export($data, true));
        
        // сохраняем данные в simulations_dialogs_points
        foreach($data as $pointId=>$item) {
            Logger::debug("check exist simId {$simId} pointId {$pointId}");
            
            $sql = "select count(*) as count from simulations_dialogs_points where sim_id={$simId} and point_id={$pointId}";
            Logger::debug("sql : $sql");
            $command = $connection->createCommand($sql);
            $row = $command->queryRow();
            Logger::debug("row : ".var_export($row, true));
            
            //$dialogsPoints = SimulationsDialogsPoints::model()->bySimulationAndPoint($simId, $pointId)->find();
            //Logger::debug("exist : ".var_export($dialogsPoints, true));
            //if ($dialogsPoints) {
            if ($row['count'] == 1) {
                Logger::debug("update for simId: $simId pointId: $pointId");
            
                $sql = "update simulations_dialogs_points set value = value + {$item['value']}, count = count + {$item['count']} where sim_id={$simId} and point_id={$pointId}";
                $command = $connection->createCommand($sql);
                $command->execute();
                
                /*
                $dialogsPoints->value += $item['value'];
                $dialogsPoints->count += $item['count'];
                $dialogsPoints->save();*/
            }
            else {
                Logger::debug("insert for simId: $simId pointId: $pointId value: {$item['value']} count: {$item['count']}");
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
