<?php

namespace application\components\Logging;

class OverallRateTableTableAnalysis2 extends LogTable {

    public $language = ["management"  => "Управленческие навыки",
        "performance" => "Результативность",
        "time"        => "Эффективность использования времени",
        "overall"     => "Итоговый рейтинг",
    ];

    public function getHeaders()
    {
        return [
            'Тип оценки',
            'Оценка'
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
            $this->getCategoryCodeName($rate),
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

    public function getCellValueFormat($columnNo, $textLabel = null) {
        if (1 == $columnNo) {
            return \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00;
        } else {
            return \PHPExcel_Style_NumberFormat::FORMAT_TEXT;
        }
    }
}