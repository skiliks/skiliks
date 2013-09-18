<?php

namespace application\components\Logging;

class OverallRateTable extends LogTable {

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
        return 'Overall rate';
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