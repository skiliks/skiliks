<?php



/**
 * Сервис  по работе с симуляциями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationService {
    
    /**
     * Получить идентификатор симуляции для заданого пользователя
     * @param int $uid
     * @return int
     */
    public static function get($uid) {
        $simulation = Simulations::model()->byUid($uid)->find();
        if (!$simulation) return false;
        return $simulation->id;
    }
    
    /**
     * Определение типа симуляции
     * @param int $sid
     * @return int
     */
    public static function getType($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) return false;
        return $simulation->type;
    }
    
    public static function getUid($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) return false;
        return $simulation->user_id;
    }
    
    /**
     * Определяет игровое время в рамках заданной симуляции
     * @param int $simId 
     * @return int игровое время
     */
    public static function getGameTime($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) throw new Exception('Не могу определить симуляцию');
        $startTime = $simulation->start;
        
        $variance = time() - $simulation->start;
        $variance = $variance*SKILIKS_SPEED_FACTOR;

        $unixtimeMins = round($variance/60) + 9*60;
        return $unixtimeMins;
    }
    
    /**
     * Рассчет оценки по окончании симуляции
     */
    public static function calcPoints($simId) {
        //$documentId = ExcelDocumentService::getIdByName('Сводный бюджет', $simId);
        
        $documentId = ExcelDocumentService::getIdByFileCode('D1', $simId);
        //echo('documentId:'); var_dump($documentId);
        if (!$documentId) return false;
        
        $document = ExcelFactory::getDocument($documentId);
        if (!$document) return false;
        $worksheetId = $document->getWorksheetIdByName('Сводный');
        $worksheet = $document->loadWorksheet($worksheetId);
        
        $excelFormula = new ExcelFormula();
        $excelFormula->setWorksheet($worksheet);
        
        // загрузим очки пользователя
        $points = 0;
        $model = SimulationsExcelPoints::model()->bySimulation($simId)->find();
        if ($model) {
            $points = $model->value;
        }
        
        $pointsMap = array();
        
        Logger::debug("start excel check");
        /**$formula = '=SUM(N6:Q7)+SUM(N10:Q14)';
        $value = $excelFormula->parse($formula);
        if ($value == 13707993) {
            $points++;
            $pointsMap[1] = 1;
        }
        else {
            $pointsMap[1] = 0;
        }
        
        $formula = '=SUM(N6:Q7)+SUM(N10:Q14)-SUM(N8:Q8)-SUM(N15:Q15)';
        $value = $excelFormula->parse($formula);
        if ($value == 0) {
            $points++;
            $pointsMap[2] = 1;
        }
        else {
            $pointsMap[2] = 0;
        }
        
        $formula = '=SUM(R6:R7)+SUM(R10:R14)';
        $value = $excelFormula->parse($formula);
        if ($value == 13707993) {
            $points++;
            $pointsMap[3] = 1;
        }
        else {
            $pointsMap[3] = 0;
        }
        
        $formula = '=SUM(R6:R7)+SUM(R10:R14)-R8-R15';
        $value = $excelFormula->parse($formula);  //0
        if ($value == 0) {
            $points++;
            $pointsMap[4] = 1;
        }
        else {
            $pointsMap[4] = 0;
        }*/
        
        // новые 
        $document->activateWorksheetByName('Логистика');
        $formula = '=SUM(B6:M7)+SUM(B10:M14)';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 876264) {
            $points++;
            $pointsMap[1] = 1;
        }
        else {
            $pointsMap[1] = 0;
        }
        
        $document->activateWorksheetByName('Производство');
        $formula = '=SUM(B6:M7)+SUM(B10:M14)';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 876264) {
            $points++;
            $pointsMap[2] = 1;
        }
        else {
            $pointsMap[2] = 0;
        }
        
        $document->activateWorksheetByName('Сводный');
        $formula = '=SUM(N6:Q7)+SUM(N10:Q14)-SUM(B6:M7)-SUM(B10:M14)';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 0) {
            $points++;
            $pointsMap[3] = 1;
        }
        else {
            $pointsMap[3] = 0;
        }
        
        $formula = '=SUM(R6:R7)+SUM(R10:R14)-SUM(B6:M7)-SUM(B10:M14)';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 0) {
            $points++;
            $pointsMap[4] = 1;
        }
        else {
            $pointsMap[4] = 0;
        }
        
        $formula = '=SUM(N16:Q16)-(SUM(B8:M8)-SUM(B15:M15))';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 0) {
            $points++;
            $pointsMap[5] = 1;
        }
        else {
            $pointsMap[5] = 0;
        }
        
        
        $formula = '=R16-(SUM(B8:M8)-SUM(B15:M15))';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 0) {
            $points++;
            $pointsMap[6] = 1;
        } 
        else {
            $pointsMap[6] = 0;
        }
        
        $formula = '=R18';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 0.597951) {
            $points++;
            $pointsMap[7] = 1;
        }    
        else {
            $pointsMap[7] = 0;
        }
        
        $formula = '=SUM(N19:Q19)';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 1.547943) {
            $points++;
            $pointsMap[8] = 1;
        }    
        else {
            $pointsMap[8] = 0;
        }
        
        
        $formula = '=SUM(N20:Q20)';
        $value = $excelFormula->parse($formula); 
        //echo("value = $value <br/>");
        if ($value == 0.676173) {
            $points++;
            $pointsMap[9] = 1;
        }        
        else {
            $pointsMap[9] = 0;
        }
        
        foreach($pointsMap as $formulaId=>$point) {
            CalculationEstimateService::addExcelPoint($simId, $formulaId, $point);
        }
        
        // сохраняем
        /*if (!$model) {
            $model = new SimulationsExcelPoints();
            $model->sim_id = $simId;
        }    
        $model->value = $points;
        $model->save();*/
        return true;
    }
    
    /**
     * Установка флага в рамках симуляции
     * @param int $simId
     * @param string $flag 
     */
    public static function setFlag($simId, $flag) {
        $model = SimulationFlagsModel::model()->bySimulation($simId)->byFlag($flag)->find();
        if (!$model) {
            $model = new SimulationFlagsModel();
            $model->sim_id = $simId;
            $model->flag = $flag;
        }
        
        $model->value = 1;
        $model->save();
    }
    
    /**
     * Получить список флагов в рамках симуляции
     * @param int $simId
     * @return array
     */
    public static function getFlags($simId) {
        $flags = SimulationFlagsModel::model()->bySimulation($simId)->findAll();
        
        $list = array();
        foreach($flags as $flag) {
            $list[$flag->flag] = $flag->value;
        }
        
        return $list;
    }
}

?>
