<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 11.03.13
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */

namespace application\components\Logging;


class TimeManagementTable extends LogTable {

    public function getHeaders()
    {
        return [
            'Парамерт',
            'Оценка',
            'единица измерения',
            'Системный псевдоним',
        ];

    }

    public function getId() {
        return 'time-management-table';
    }

    public function getTitle()
    {
        return 'Time management';
    }

    protected function getRow($item)
    {
        return [
            $item->getLabel(),
            $item->value,
            \Yii::t('site', $item->unit_label),
            $item->slug,
            ];
    }

    /**
     * @param $point
     * @return string
     */
    public function getRowId($row)
    {
        return sprintf(
            'time-management-%s ',
            $row[3]
        );
    }
}