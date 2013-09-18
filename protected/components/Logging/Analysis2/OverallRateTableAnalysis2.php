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
            $rate->value . '%'
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'overall-rate-%s ',
            $row[0]
        );
    }
}