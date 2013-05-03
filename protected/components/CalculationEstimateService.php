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
    public static function addExcelPoint($simId, $formulaId, $point)
    {
        $model = SimulationExcelPoint::model()->bySimulation($simId)->byFormula($formulaId)->find();
        if (!$model) {
            $model = new SimulationExcelPoint();
            $model->sim_id = $simId;
            $model->formula_id = $formulaId;
        }
        $model->value = $point;
        $model->save();
    }
}


