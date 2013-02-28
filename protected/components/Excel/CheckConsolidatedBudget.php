<?php

/**
 * @see: Dropbox/ProjectX - Development/1. Documentation/
 *    1.1 Scenario/1.1.3 Для заливки/Documents/Оценка Excel/Сводный бюджет_02_v23 (+формулы для проверки)_v3.xlsx
 *  v3 or upper
 * @author slavka
 */
class CheckConsolidatedBudget 
{
    /**
     * @var integer
     */
    private $userPoints = 0;
    
    /**
     * @var array of integer, key - check rule id
     */    
    private $userPointsMap = array();
    
    /**
     * @var integer
     */
    private $simId = null;
    
    /**
     * @var mixed array
     */
    private $configs = array();
    
    /**
     * Consolidated budget full file name
     */
    const CONSOLIDATE_BUDGET_FILENAME = 'Сводный_бюджет_02_v23.xls';

    /**
     * @param integer $simId
     */
    public function __construct($simId) {
        $this->simId = $simId;
    }
    
    /**
     * 
     */
    public function resetUserPoints()
    {
        $this->userPoints = 0;
        $this->userPointsMap = array();
        
        $simulationsExcelPoints = SimulationExcelPoint::model()
            ->bySimulation($this->simId)
            ->find();
        
        if (0 === count($simulationsExcelPoints)) {
            foreach (ExcelPointFormula::model()->findAll() as $formula) {
                $this->userPointsMap[$formula->id] = 0;
            }  
        }
    }
    
    /**
     * @see: check ID 1
     */
    public function checkNo1($whLogistic)
    {
        $sum = 0;
        
        $s1 = $this->SUM($whLogistic, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(6,7));
        $s2 = $this->SUM($whLogistic, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(10,11,12,13,14));
        
        $sum = $s1 + $s2;
        
        if (round($sum) == (int)$this->configs['etalons'][1]) {
            $this->userPoints++;
            $this->userPointsMap[1] = 1;
        } else {
            $this->userPointsMap[1] = 0;
        }    
        
        return $this;
    }
    
    /**
     * @see: check ID 2
     */
    public function checkNo2($whProduction)
    {
        $sum = 0;
        
        $s1 = $this->SUM($whProduction, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(6,7));
        $s2 = $this->SUM($whProduction, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(10,11,12,13,14));
        
        $sum = $s1 + $s2;
        
        // for some reason $sum is float and $etalin is integer
        // and even if $sum == $etalon, PHP deside that $sum != $etalon
        if (round($sum) == (int)$this->configs['etalons'][2]) {
            $this->userPoints++;
            $this->userPointsMap[2] = 1;
        } else {
            $this->userPointsMap[2] = 0;
        }    
        
        return $this;
    }
    
    /**
     * @see: check ID 3
     */
    public function checkNo3($whConsolidated)
    {
        $sum = 0;
        
        $s1 = $this->SUM($whConsolidated, array('N','O','P','Q'), array(6,7));
        $s2 = $this->SUM($whConsolidated, array('N','O','P','Q'), array(10,11,12,13,14));
        $s3 = $this->SUM($whConsolidated, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(6,7));
        $s4 = $this->SUM($whConsolidated, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(10,11,12,13,14));
        
        $sum = $s1 + $s2 - $s3 - $s4;
        
        // for some reasons $sum is float and != 0, but = 0,00...001
        if (round($sum) == $this->configs['etalons'][3]) {
            $this->userPoints++;
            $this->userPointsMap[3] = 1;
        } else {
            $this->userPointsMap[3] = 0;
        }    
        
        return $this;   
    }
    
    /**
     * @see: check ID 4
     */
    public function checkNo4($whConsolidated)
    {
        $sum = 0;
        
        $s1 = $this->SUM($whConsolidated, array('R'), array(6,7));
        $s2 = $this->SUM($whConsolidated, array('R'), array(10,11,12,13,14));
        $s3 = $this->SUM($whConsolidated, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(6,7));
        $s4 = $this->SUM($whConsolidated, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(10,11,12,13,14));
        $sum = $s1 + $s2 - $s3 - $s4;
        
        // for some reasons $sum is float and != 0, but = 0,00...001
        if (round($sum) == $this->configs['etalons'][4]) {
            $this->userPoints++;
            $this->userPointsMap[4] = 1;
        } else {
            $this->userPointsMap[4] = 0;
        }    
        
        return $this;   
    }
    
    /**
     * @see: check ID 5
     */
    public function checkNo5($whConsolidated)
    {
        $sum = 0;
        
        $s1 = $this->SUM($whConsolidated, array('N','O','P','Q'), array(16));
        $s2 = $this->SUM($whConsolidated, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(8));
        $s3 = $this->SUM($whConsolidated, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(15));
        
        $sum = $s1 - ($s2 - $s3);
        
        // for some reasons $sum is float and != 0, but = 0,00...001
        if (round($sum) == $this->configs['etalons'][5]) {
            $this->userPoints++;
            $this->userPointsMap[5] = 1;
        } else {
            $this->userPointsMap[5] = 0;
        }    
        
        return $this;   
    }
    
    /**
     * @see: check ID 6
     */
    public function checkNo6($whConsolidated)
    {
        $sum = 0;
        
        $s1 = $whConsolidated->getCell('R16')->getCalculatedValue();
        $s2 = $this->SUM($whConsolidated, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(8));
        $s3 = $this->SUM($whConsolidated, array('B','C','D','E','F','G','H','I','J','K','L','M'), array(15));
        
        $sum = $s1 - ($s2 - $s3);
        
        // for some reasons $sum is float and != 0, but = 0,00...001
        if ((int)$sum == $this->configs['etalons'][6]) {
            $this->userPoints++;
            $this->userPointsMap[6] = 1;
        } else {
            $this->userPointsMap[6] = 0;
        }    
        
        return $this;   
    }
    
    /**
     * @see: check ID 7
     */
    public function checkNo7($whConsolidated)
    {
        $sum = $whConsolidated->getCell('R18')->getCalculatedValue();
        
        $sum = $sum*1000000;
        $check = $this->configs['etalons'][7]*1000000;
        
        // Client want accuracy: 6 numbers after point 
        if (round($sum) == (int)$check) {
            $this->userPoints++;
            $this->userPointsMap[7] = 1;
        } else {
            $this->userPointsMap[7] = 0;
        }    
        
        return $this;   
    }
    
    /**
     * @see: check ID 8
     */
    public function checkNo8($whConsolidated)
    {
        $sum = $this->SUM($whConsolidated, array('N','O','P','Q'), array(19));
        $sum = $sum*1000000;
        
        $check = $this->configs['etalons'][8]*1000000;
        
        // Client want accuracy: 6 numbers after point 
        if (round($sum) == (int)$check) {
            $this->userPoints++;
            $this->userPointsMap[8] = 1;
        } else {
            $this->userPointsMap[8] = 0;
        }    
        
        return $this;   
    }
    
    /**
     * @see: check ID 9
     */
    public function checkNo9($whConsolidated)
    {
        $sum = $this->SUM($whConsolidated, array('N','O','P','Q'), array(20));
        $sum = $sum*1000000;
        
        $check = $this->configs['etalons'][9]*1000000;
        
        if (round($sum) == (int)$check) {
            $this->userPoints++;
            $this->userPointsMap[9] = 1;
        } else {
            $this->userPointsMap[9] = 0;

        }    
        
        return $this;   
    }
    
    /**
     * PHPExcel provide SUM only as cell operation.
     * I add this method to avoid edit user document
     * 
     * @param PHPExcel.Sheet $worksheet
     * @param array of strings $colums
     * @param array of strings || integers $rows
     * 
     * @return float
     */
    private function SUM($worksheet, $colums, $rows) 
    {
        $sum = 0;
        
        foreach ($colums as $colName) {
            foreach ($rows as $rowName) {
                $sum += $worksheet->getCell($colName.$rowName)->getCalculatedValue();
            }
        } 
        
        return $sum;
    }
    
    /**
     * Рассчет оценки по окончании симуляции
     * 
     * @param integer simId
     */
    public function calcPoints() 
    {
        // check document {
        $documentId = MyDocument::model()->findByAttributes([
            'code' => 'D1'
        ]);
        if (null === $documentId) {
            return false;
        }

        $zohoDoc = new ZohoDocuments($this->simId, $documentId, null); // template name isn`t so important here

        //$documentPath = ExcelFactory::getDocumentPath($this->simId, $documentId, self::CONSOLIDATE_BUDGET_FILENAME);
        $documentPath = $zohoDoc->getUserFilepath();
        
        if (null === $documentPath) {
            return false;
        }
        // check document }
        
        // init configs {
        $params = Yii::app()->params['analizer'];
        $this->configs = $params['excel']['consolidatedBudget']; // We don`t sure is PHP 5.3 used on server.
        $worksheetNames = $this->configs['worksheetNames'];
        // init configs }
        
        // get workSheets {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($documentPath);
        } catch (Exception $e) {
            $this->resetUserPoints();
            $this->savePoints();
            
            return false;
        }        
        // 'wh' - worksheet
        $whLogistic     = $objPHPExcel->getSheetByName($worksheetNames['logistic']);
        $whProduction   = $objPHPExcel->getSheetByName($worksheetNames['production']);
        $whConsolidated = $objPHPExcel->getSheetByName($worksheetNames['consolidated']);
        // get workSheets }

        if (NULL === $whLogistic || NULL === $whProduction || NULL === $whConsolidated) {
            $this->resetUserPoints();
            $this->savePoints();
            Yii::log('no sheet', 'warning');
            die('223');
            return false;   
        }
        
        // start analyze {
        $this->resetUserPoints();
        
        $this->checkNo1($whLogistic)
             ->checkNo2($whProduction)
             ->checkNo3($whConsolidated)
             ->checkNo4($whConsolidated)
             ->checkNo5($whConsolidated)
             ->checkNo6($whConsolidated)
             ->checkNo7($whConsolidated)
             ->checkNo8($whConsolidated)
             ->checkNo9($whConsolidated);
        // start analyze }
        
        
        
        // save results
        $this->savePoints();
        
        return true;
    }
    
    /**
     * 
     */
    public function savePoints()
    {
        foreach($this->userPointsMap as $formulaId => $pointsValue) {
            CalculationEstimateService::addExcelPoint($this->simId, $formulaId, $pointsValue);
        }    
    }
}

