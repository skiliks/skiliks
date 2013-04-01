<?php

namespace application\components\Logging;

class PerformanceTable extends LogTable {

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
        return 'Performance points';
    }

    protected function getRow($point)
    {
        return [
            $point->performance_rule_id,
            $point->performanceRule->value
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'performance-rule-%s ',
            $row[0]
        );
    }
}