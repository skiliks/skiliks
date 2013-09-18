<?php

namespace application\components\Logging;

class PerformanceTableAnalysis2 extends LogTable {

    public function getHeaders()
    {
        return [
            'Rule id',
            'Value'
        ];
    }

    public function getId() {
        return 'performance-points';
    }

    public function getTitle()
    {
        return 'Результативность';
    }

    protected function getRow($point)
    {
        /* @var $point PerformanceRule */
        return [
            $point->performanceRule->code,
            $point->performanceRule->value/100
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'performance-rule-%s ',
            $row[0]
        );
    }

    public function getCellValueFormat($columnNo, $rowNo = null) {
        if (1 == $columnNo) {
            return \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00;
        } else {
            return \PHPExcel_Style_NumberFormat::FORMAT_TEXT;
        }
    }
}