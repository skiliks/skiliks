<?php



/**
 * Сервис  по работе с симуляциями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationService {
    
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
     * @throws Exception
     * @return int игровое время
     */
    public static function getGameTime($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        // Logger::debug("getGameTime : sim {$simulation->id}");
        if (!$simulation) throw new Exception('Не могу определить симуляцию');
        $startTime = $simulation->start;
        
        $variance = time() - $simulation->start;
        $variance = $variance * Yii::app()->params['skiliksSpeedFactor'];

        $unixtimeMins = round($variance/60) + 9*60;
        return $unixtimeMins;
    }
    
    /**
     * Рассчет оценки по окончании симуляции
     * 
     * @param integer simId
     */
    public static function calcPoints($simId) 
    {
        $documentId = ExcelDocumentService::getIdByFileCode('D1', $simId);
        if (null === $documentId) {
            return false;
        }
        
        $documentPath = ExcelFactory::getDocumentPath($simId, $documentId);
        if (null === $documentPath) {
            return false;
        }
        
        $params = Yii::app()->params['analizer'];
        $params = $params['excel']['consolidatedBudget']; // We don`t sure is PHP 5.3 used on server.
        
        $objPHPExcel = PHPExcel_IOFactory::load($documentPath);
        
        // 'wh' - worksheet
        $whLogistic = $objPHPExcel->getSheetByName($params['logisticWorksheetName']);
        $whProduction = $objPHPExcel->getSheetByName($params['production WorksheetName']);
        $whConsolidated = $objPHPExcel->getSheetByName($params['consolidatedWorksheetName']);
        
        $points = 0;
        $model = SimulationsExcelPoints::model()->bySimulation($simId)->find();
        if ($model) {
            $points = $model->value;
        }
        
        $pointsMap = array();
        
        // ---
        
        $tmpSum = 0;
        foreach(array('B','C','D','E','F','G','H','I','J','K','L','M') as $colName) {
            $tmpSum += $whLogistic->getCell($colName.'6') + $whLogistic->getCell($colName.'7');
        }
        if ($tmpSum == $params['etalons'][1]) {
            $points++;
            $pointsMap[1] = 1;
        }
        else {
            $pointsMap[1] = 0;
        }
        
        var_dump($points, $pointsMap);
        
        die;
        
        // ---------------
        
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
        
        // новые 
        $document->activateWorksheetByName('Логистика');
        $formula = '=SUM(B6:M7)+SUM(B10:M14)';
        $value = $excelFormula->parse($formula); 

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


        if ((int)$value == 3303417) {
            $points++;
            $pointsMap[2] = 1;
        }
        else {
            $pointsMap[2] = 0;
        }
        
        $document->activateWorksheetByName('Сводный');
        $formula = '=SUM(N6:Q7)+SUM(N10:Q14)-SUM(B6:M7)-SUM(B10:M14)';
        $value = $excelFormula->parse($formula); 

        if ($value == 0) {
            $points++;
            $pointsMap[3] = 1;
        }
        else {
            $pointsMap[3] = 0;
        }
        
        $formula = '=SUM(R6:R7)+SUM(R10:R14)-SUM(B6:M7)-SUM(B10:M14)';
        $value = $excelFormula->parse($formula); 

        if ($value == 0) {
            $points++;
            $pointsMap[4] = 1;
        }
        else {
            $pointsMap[4] = 0;
        }
        
        $formula = '=SUM(N16:Q16)-(SUM(B8:M8)-SUM(B15:M15))';
        $value = $excelFormula->parse($formula); 

        if ($value == 0) {
            $points++;
            $pointsMap[5] = 1;
        }
        else {
            $pointsMap[5] = 0;
        }
        
        
        $formula = '=R16-(SUM(B8:M8)-SUM(B15:M15))';
        $value = $excelFormula->parse($formula); 

        if ($value == 0) {
            $points++;
            $pointsMap[6] = 1;
        } 
        else {
            $pointsMap[6] = 0;
        }
        
        $formula = '=R18';

        $value = $excelFormula->parse($formula); 


        if ($value == 0.597951) {
            $points++;
            $pointsMap[7] = 1;
        }    
        else {
            $pointsMap[7] = 0;
        }
        
        $formula = '=SUM(N19:Q19)';
        $value = $excelFormula->parse($formula); 

        if ($value == 1.547943) {
            $points++;
            $pointsMap[8] = 1;
        }    
        else {
            $pointsMap[8] = 0;
        }
        
        
        $formula = '=SUM(N20:Q20)';
        $value = $excelFormula->parse($formula); 

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


