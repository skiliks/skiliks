<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 08.03.13
 * Time: 7:49
 * To change this template use File | Settings | File Templates.
 */

class ActivityLogTable extends LogTable {
    public function getHeaders()
    {
        return ['Window Start Time',	'Window End Time',	'Leg type',	'Leg action',	'Activity ID',	'Category ID'];
    }

    public function getTitle()
    {
        return 'Activity Log';
    }

    public function getId() {
        return 'activity-log';
    }

    /**
     * @param $logActivityAction LogActivityAction
     * @return array
     */
    public function getRow($logActivityAction)
    {
        $action = $logActivityAction->activityAction->getAction();
        return [
            $logActivityAction->start_time,
            $logActivityAction->end_time,
            $logActivityAction->activityAction->leg_type,
            $action ? $action->getCode() : '',
            $logActivityAction->activityAction->activity->primaryKey,
            $logActivityAction->activityAction->activity->category->code
        ];
    }
}