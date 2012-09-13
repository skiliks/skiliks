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
        $simulationTime = time() - $startTime;  // сколько времени реально длится симуляция
        
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
        
        //$formula = '=SUM(N6:Q7)+SUM(N10:Q14)';
        //$formula = '=SUM(N6:Q7)+SUM(N10:Q14)-SUM(N8:Q8)-SUM(N15:Q15)';
        $formula = '=SUM(R6:R7)+SUM(R10:R14)';
        //$formula = '=SUM(R6:R7)+SUM(R10:R14)-R8-R15';
        //$formula = '=SUM(B4;C4)';
        $value = $excelFormula->parse($formula);
        
        var_dump($value);
    }
}

?>
