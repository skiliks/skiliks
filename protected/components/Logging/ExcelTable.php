<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 11.03.13
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */

namespace application\components\Logging;


class ExcelTable extends LogTable {

    public function getHeaders()
    {
        return [
            'Номер формулы',
            'Оценка (0 или 1)'
        ];

    }

    public function getId() {
        return 'excel-points';
    }

    public function getTitle()
    {
        return 'Excel points';
    }

    protected function getRow($point)
    {
        return [
            $point->formula_id,
            $point->value
            ];
    }
}