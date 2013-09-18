<?php

namespace application\components\Logging;

class PerformanceAggregatedTableAnalysis2 extends LogTable {

    public function __construct($logs) {
        $tmpArr = [];

        foreach ([0,1,2,3] as $id) {
            $tmp = new \stdClass();
            $tmp->category_id = $id;
            $tmp->value = 0;
            $tmpArr[$id] = $tmp;
        }

        foreach ($logs as $log) {
            $key = (int)$log->category_id;
            if ('2_min' == $log->category_id) {
                $key = 3;
            }

            $tmp = new \stdClass();
            $tmp->category_id = $log->category_id;
            $tmp->value = $log->value;

            $tmpArr[$key] = $tmp;
        }

        ksort($tmpArr);

        $this->logs = $tmpArr;
    }

    public function getHeaders()
    {
        return [
            'Группа задач',
            "Результативность,\n оценка (0-100%)"
        ];
    }

    public function getHeaderWidth() {
        return [
            24,
            24,
            14,
            20,
            20
        ];
    }

    public function getId() {
        return 'performance-aggregated';
    }

    public function getTitle()
    {
        return 'Результативность';
    }

    public $gorupLabels = [
        1 => 'Высокий приоритет',
        0 => 'Срочно',
        2 => 'Средний приоритет',
        3 => 'Прочее',
    ];

    /**
     * @param stdClass $item
     * @return array
     */
    protected function getRow($item)
    {
        return [
            $this->gorupLabels[$item->category_id],
            $item->value/100,
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'performance-aggregated-%s ',
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