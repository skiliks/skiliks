<?php



/**
 * Контроллер формирования результатов расчета оценки по экселю.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelPointsController extends AjaxController{
    
    public function actionDraw() 
    {
        $simulation = $this->getSimulationEntity();

        return $this->sendJSON(array(
            'result' => 1,
            'data'   => CalculationEstimateService::getExcelPointsValies($simulation)
        ));
    }
}