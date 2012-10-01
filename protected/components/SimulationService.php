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
     * Определяет игровое время в рамках заданной симуляции
     * @param int $simId 
     * @return int игровое время
     */
    public static function getGameTime($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) throw new Exception('Не могу определить симуляцию');
        $startTime = $simulation->start;
        
        #########3
        $variance = time() - $simulation->start;
        $variance = $variance*4;

        $unixtimeMins = round($variance/60) + 9*60;
        return $unixtimeMins;
        ########################
        
        $time = time();
        Logger::debug("getGameTime : startTime $startTime");
        Logger::debug("getGameTime : time $time");
        $simulationTime = time() - $startTime;  // сколько времени реально длится симуляция
        Logger::debug("getGameTime : simulationTime $simulationTime");
        
        return $simulationTime * 4; // возвращаем игровое время
    }
    
    /**
     * Рассчет оценки по окончании симуляции
     */
    public static function calcPoints($simId) {
        $documentId = ExcelDocumentService::getIdByName('Сводный бюджет', $simId);
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
        
        Logger::debug("start excel check");
        $formula = '=SUM(N6:Q7)+SUM(N10:Q14)';
        $value = $excelFormula->parse($formula);
        if ($value == 13707993) {
            $points++;
        }
        
        
        $formula = '=SUM(N6:Q7)+SUM(N10:Q14)-SUM(N8:Q8)-SUM(N15:Q15)';
        $value = $excelFormula->parse($formula);
        if ($value == 0) {
            $points++;
        }
        
        $formula = '=SUM(R6:R7)+SUM(R10:R14)';
        $value = $excelFormula->parse($formula);
        if ($value == 13707993) {
            $points++;
        }
        
        $formula = '=SUM(R6:R7)+SUM(R10:R14)-R8-R15';
        $value = $excelFormula->parse($formula);  //0
        if ($value == 0) {
            $points++;
        }
        
        
        // сохраняем
        if (!$model) {
            $model = new SimulationsExcelPoints();
            $model->sim_id = $simId;
        }    
        $model->value = $points;
        $model->save();
    }
    
    /**
     * Установка флага в рамках симуляции
     * @param int $simId
     * @param string $flag 
     */
    public static function setFlag($simId, $flag) {
        $model = SimulationFlagsModel::model()->bySimulation($simId)->byFlag($flag)->find();
        if ($model) return true; // флаг уже установлен
        
        $model = new SimulationFlagsModel();
        $model->sim_id = $simId;
        $model->flag = $flag;
        $model->value = 1;
        $model->insert();
    }
}

?>
