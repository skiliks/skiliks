<?php

namespace application\components\Logging;

class OverallRateTable extends LogTable {

    public function getHeaders()
    {
        return [
            'Category code',
            'Percent'
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
            $rate->assessment_category_code,
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