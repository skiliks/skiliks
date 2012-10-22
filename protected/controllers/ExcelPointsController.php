<?php



/**
 * Контроллер формирования результатов расчета оценки по экселю.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelPointsController extends AjaxController{
    
    public function actionDraw() {
        $sid = Yii::app()->request->getParam('sid', false);  
        if (!$sid) throw new Exception('wrong sid');
        
        $simId = SessionHelper::getSimIdBySid($sid);
        
        $formulaCollection = ExcelPointsFormulaModel::model()->findAll();
        $formulaList = array();
        foreach($formulaCollection as $formulaModel) {
            $formulaList[$formulaModel->id] = $formulaModel->formula;
        }
        
        $excelPoints = SimulationsExcelPoints::model()->bySimulation($simId)->findAll();
        $list = array();
        foreach($excelPoints as $excelPoint) {
            if (!isset($formulaList[$excelPoint->formula_id])) continue;
            
            $formula = $formulaList[$excelPoint->formula_id];
            $list[] = array(
                'formula' => $formula,
                'value' => $excelPoint->value
            );
        }
        
        $result = array();
        $result['result'] = 1;
        $result['data'] = $list;

        return $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
