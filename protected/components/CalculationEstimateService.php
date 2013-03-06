<?php


/**
 * Сервис расчет оценки
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CalculationEstimateService 
{
    /**
     * Добавить поинт по экселю
     * @param int $simId
     * @param int $formulaId
     * @param int $pointId 
     */
    public static function addExcelPoint($simId, $formulaId, $point) {
        $model = SimulationExcelPoint::model()->bySimulation($simId)->byFormula($formulaId)->find();
        if (!$model) {
            $model = new SimulationExcelPoint();
            $model->sim_id      = $simId;
            $model->formula_id  = $formulaId;
        }
        $model->value = $point;
        $model->save();
    }
    
    /**
     * @param Simulation $simulation
     * @return mixed array
     */
    public static function getExcelPointsValies($simulation)
    {
        $formulaCollection = ExcelPointFormulal::model()->findAll();
        
        $formulaList = array();
        
        foreach($formulaCollection as $formulaModel) {
            $formulaList[$formulaModel->id] = array(
                'formula' => $formulaModel->formula, 
                'value'   => 0
            );
        }
        
        $excelPoints = SimulationExcelPoint::model()->bySimulation($simulation->id)->findAll();
        
        $list = array();
        
        foreach($excelPoints as $excelPoint) {            
            if (isset($formulaList[$excelPoint->formula_id])) {
                $formulaList[$excelPoint->formula_id]['value'] = $excelPoint->value;
            }
        }
        
        return $formulaList;
    }
}


