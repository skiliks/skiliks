<?php

namespace application\components\Logging;


class ActivityAggregated214dTable extends LogTable
{
    public function getId()
    {
        return 'activity-aggregated-214d';
    }

    public function getTitle()
    {
        return 'Leg_actions - aggregated 214d';
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
            'Keep last category',
            'Игровое время - start',
            'Игровое время - end',
            'Time diff'
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
            $row->is_keep_last_category === '0' ? 'yes' : '',
            $row->start_time,
            $row->end_time,
            $diff
        ];
    }


    public function getRowId($logMail)
    {
        return '';
    }
}