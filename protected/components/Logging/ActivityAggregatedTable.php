<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 11.03.13
 * Time: 17:54
 * To change this template use File | Settings | File Templates.
 */

namespace application\components\Logging;


class ActivityAggregatedTable extends LogTable
{
    public function getId()
    {
        return 'activity-aggregated';
    }

    public function getTitle()
    {
        return 'Leg_actions - aggregated';
    }

    public function getHeaders()
    {
        return [
            'Leg_type',
            'Leg_action',
            'activity ID',
            'Parent',
            'Grandparent',
            'Category',
            'Игровое время - start',
            'Игровое время - end',
            'Time diff',
            'Keep last category'
        ];
    }

    /**
     * @param \LogActivityActionAgregated $row
     * @return array
     */
    protected function getRow($row)
    {
        static $end_time = 0;
        $diff = ($end_time === 0)?'-':strtotime($row->start_time) - strtotime($end_time);
        $end_time = $row->end_time;
        return [
            $row->leg_type,
            $row->leg_action,
            $row->activityAction->activity->code,
            $row->activityAction->activity->parent,
            $row->activityAction->activity->grandparent,
            $row->activityAction->activity->category->code,
            $row->start_time,
            $row->end_time,
            $diff,
            $row->keep_last_category_after_60_sec
        ];
    }


    public function getRowId($logMail)
    {
        return '';
    }
}