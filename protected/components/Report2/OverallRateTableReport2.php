<?php

use application\components\Logging\LogTable;

class OverallRateTableReport2 extends LogTable {

    public $language = [
        "management"  => "Управленческие навыки",
        "performance" => "Результативность",
        "time"        => "Эффективность использования времени",
        "overall"     => "Итоговый рейтинг",
        "percentile"  => "Процентиль",
    ];

    public function getHeaders()
    {
        return [
            'Тип оценки',
            'Оценка'
        ];
    }

    public function getHeaderWidth() {
        return [
            24,
            24,
            14,
            40,
            14
        ];
    }

    public function getId() {
        return 'overall-rate';
    }

    public function getTitle()
    {
        return 'Итоговый рейтинг';
    }

    /**
     * @param \AssessmentOverall $rate
     * @return array
     */
    protected function getRow($rate)
    {
        return [
            $this->language[$rate->assessment_category_code],
            $rate->value/100
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'overall-rate-%s ',
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