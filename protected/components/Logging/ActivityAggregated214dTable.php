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
            'Keep last category - Initial',
            'Keep last category - after 60 real sec test',
            'Игровое время - start',
            'Игровое время - end',
            'Длительность, игровых мин',
            'Time diff'
        ];
    }

    /**
     * @param \LogActivityActionAgregated214d $row
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
            (null === $row->activityAction)?'':$row->activityAction->activity->code,
            $row->parent,
            (null === $row->activityAction)?'':$row->activityAction->activity->grandparent,
            $row->category,
            ($row->keep_last_category_initial === \LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_YES)?'yes':'',
            ($row->keep_last_category_after === \LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_YES)?'yes':'',
            $row->start_time,
            $row->end_time,
            $row->duration,
            $diff
        ];
    }


    public function getRowId($logMail)
    {
        return '';
    }
}