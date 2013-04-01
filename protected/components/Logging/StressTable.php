<?php

namespace application\components\Logging;

class StressTable extends LogTable {

    public function getHeaders()
    {
        return [
            'Rule id',
            'Value'
        ];
    }

    public function getId() {
        return 'stress-points';
    }

    public function getTitle()
    {
        return 'Stress points';
    }

    protected function getRow($point)
    {
        return [
            $point->stress_rule_id,
            $point->stressRule->value
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'stress-rule-%s ',
            $row[0]
        );
    }
}