<?php



/**
 * Сервис расчета оценки по письму
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailEstimateService {
    
    public static function calculate($mailId, $simId) {
        $sql = "select 
                    cp.point_id,
                    cp.add_value,
                    cpt.scale,
                    cpt.code    
                from mail_points as cp
                left join characters_points_titles as cpt on (cpt.id = cp.point_id)
                where cp.mail_id = {$mailId}";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $data = array();
        foreach($dataReader as $row) { 
            $pointId = $row['point_id'];
            $code = (int)$row['code'][0];
            
            if (!isset($data[$pointId])) {
                $data[$pointId] = array(
                    'value'             => 0, 
                    'count'             => 0, 
                    'value_negative'    => 0, 
                    'count_negative'    => 0, 
                    'value6x'           => 0, 
                    'count6x'           => 0
                );
            }
            
            // 1X-5X
            if ($code <=5) {
                $value = $row['add_value'] * $row['scale'];
                if ($value > 0) {
                    $data[$pointId]['value'] +=  $value;
                    $data[$pointId]['count']++;
                }
                else {  // отдельо считаем отрицательную шкалу
                    $data[$pointId]['value_negative'] +=  $value;
                    $data[$pointId]['count_negative']++;
                }
            }
            
            // 6x-8x
            if ($code >=6) {
                $data[$pointId]['value6x'] +=  $row['add_value']*$row['scale'];
                $data[$pointId]['count6x']++;
            }
        }        
        
        // сохраняем данные в simulations_dialogs_points
        foreach($data as $pointId=>$item) {
            $model = SimulationMailPoint::model()->bySimulation($simId)->byPoint($pointId)->byMail($mailId)->find();
            if ($model) {
                $model->value       = $item['value'];
                $model->count       = $item['count'];
                $model->value_negative       = $item['value_negative'];
                $model->count_negative       = $item['count_negative'];
                $model->value6x     = $item['value6x'];
                $model->count6x     = $item['count6x'];
                $model->save();
            }
            else {
                $model = new SimulationDialogPoint();
                $model->sim_id      = $simId;
                $model->point_id    = $pointId;
                
                $model->value       = $item['value'];
                $model->count       = $item['count'];
                $model->value_negative       = $item['value_negative'];
                $model->count_negative       = $item['count_negative'];
                $model->value6x     = $item['value6x'];
                $model->count6x     = $item['count6x'];
                $model->insert();
            }
        }
        
        return true;
    }
}


