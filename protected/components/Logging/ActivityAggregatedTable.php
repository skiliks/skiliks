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
        return 'Leg_actions - agregate';
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
        ];
    }

    /**
     * @param \LogActivityActionAgregated $row
     * @return array
     */
    protected function getRow($row)
    {
        return [
            $row->leg_type,
            $row->leg_action,
            $row->activityAction->activity_id,
            $row->activityAction->activity->parent,
            $row->activityAction->activity->grandparent,
            $row->activityAction->activity->category->code,
            $row->is_keep_last_category === '0' ? 'yes' : '',
            $row->start_time,
            $row->end_time
        ];
    }
}