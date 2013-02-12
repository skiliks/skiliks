<?php


/**
 * Сервис расчет оценки
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CalculationEstimateService 
{
    /**
     * Расчет оценки для диалога
     * @param int $dialogId идентификатор диалога
     * @param int $simId идентификатор симуляции
     */
    public static function calculate($dialogId, $simId) {
        
        // Case 1
        $duration = 0;
        $dialogsDurations = SimulationsDialogsDurations::model()->bySimulation($simId)->find();
        if ($dialogsDurations) {
            $duration = $dialogsDurations->duration;
        }
        
        // получить duration
        $dialog = Dialogs::model()->byId($dialogId)->find();
        if (!$dialog) throw new Exception ("Cant find dialog for {$dialogId}");
        $duration += (int)$dialog->delay;
        
        $dialogs = array();
        $dialogs[] = $dialogId;
        
        
        // 2) к записи, если таковая существует, которая имеет code = code записи, полученной с фронта,  
        $dialogCollection = Dialogs::model()->byCode($dialog->code)->byStepNumber($dialog->step_number)->byReplicaNumber(0)->findAll();
        foreach($dialogCollection as $curDialog) {
            $duration += (int)$curDialog['delay'];
            $dialogs[] = $curDialog['id'];
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
        // ########################
        // расчет поинтов
        // ############################
        $sql = "select 
                    cp.point_id,
                    cp.add_value,
                    cpt.scale,
                    cpt.code    
                from characters_points as cp
                left join characters_points_titles as cpt on (cpt.id = cp.point_id)
                where cp.dialog_id in ({$dialogsStr})";
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
                    //$data[$pointId]['count']++;
                }
                else {  // отдельо считаем отрицательную шкалу
                    $data[$pointId]['value_negative'] +=  $value;
                    //$data[$pointId]['count_negative']++;
                }
            }
            
            // 6x-8x
            if ($code >=6) {
                $data[$pointId]['value6x'] +=  $row['add_value']*$row['scale'];
                //$data[$pointId]['count6x']++;
            }
        }  
        
        // сохраняем данные в simulations_dialogs_points
        foreach($data as $pointId => $item) {
            LogHelper::setLogDoialogPoint($dialogId, $simId, $pointId);
            $dialogsPoints = SimulationsDialogsPoints::model()->bySimulationAndPoint($simId, $pointId)->find();
            if (!$dialogsPoints) {
                $dialogsPoints = new SimulationsDialogsPoints();
                $dialogsPoints->sim_id      = $simId;
                $dialogsPoints->point_id    = $pointId;
            }
            
            $dialogsPoints->value       += $item['value'];
            $dialogsPoints->count       += 1;
            $dialogsPoints->value_negative       += $item['value_negative'];
            $dialogsPoints->count_negative       += 1;
            $dialogsPoints->value6x     += $item['value6x'];
            $dialogsPoints->count6x     += 1;
            $dialogsPoints->save();
            
        }
        
        return true;
    }
    
    /**
     * Добавить поинт по экселю
     * @param int $simId
     * @param int $formulaId
     * @param int $pointId 
     */
    public static function addExcelPoint($simId, $formulaId, $point) {
        $model = SimulationsExcelPoints::model()->bySimulation($simId)->byFormula($formulaId)->find();
        if (!$model) {
            $model = new SimulationsExcelPoints();
            $model->sim_id      = $simId;
            $model->formula_id  = $formulaId;
        }
        $model->value = $point;
        $model->save();
    }
    
    /**
     * @param Simulations $simulation
     * @return mixed array
     */
    public static function getExcelPointsValies($simulation)
    {
        $formulaCollection = ExcelPointsFormulaModel::model()->findAll();
        
        $formulaList = array();
        
        foreach($formulaCollection as $formulaModel) {
            $formulaList[$formulaModel->id] = array(
                'formula' => $formulaModel->formula, 
                'value'   => 0
            );
        }
        
        $excelPoints = SimulationsExcelPoints::model()->bySimulation($simulation->id)->findAll();
        
        $list = array();
        
        foreach($excelPoints as $excelPoint) {            
            if (isset($formulaList[$excelPoint->formula_id])) {
                $formulaList[$excelPoint->formula_id]['value'] = $excelPoint->value;
            }
        }
        
        return $formulaList;
    }
}


