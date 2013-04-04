<?php

namespace application\components\Logging;

class PerformanceAggregatedTable extends LogTable {

    public function getHeaders()
    {
        return [
            'Category',
            'Value',
            'Percent'
        ];
    }

    public function getId() {
        return 'performance-aggregated';
    }

    public function getTitle()
    {
        return 'Performance aggregated';
    }

    /**
     * @param \PerformanceAggregated $item
     * @return array
     */
    protected function getRow($item)
    {
        return [
            $item->category_id,
            $item->value,
            $item->percent . '%'
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'performance-aggregated-%s ',
            $row[0]
        );
    }
}