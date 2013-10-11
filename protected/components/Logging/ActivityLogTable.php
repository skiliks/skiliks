<?php
namespace application\components\Logging;
    /**
     * \addtogroup Logging
     * @{
     */
/**
 * Class ActivityLogTable
 */
class ActivityLogTable extends LogTable
{
    public function getHeaders()
    {
        return ['Window Start Time', 'Window End Time', 'Leg type', 'Leg action', 'Activity ID', 'Category ID', 'Time diff', 'Duration', 'Dialog Type'];
    }

    public function getTitle()
    {
        return 'Leg_actions - detail';
    }

    public function getId()
    {
        return 'activity-log';
    }

    /**
     * @param $logActivityAction \LogActivityAction
     * @return array
     */
    public function getRow($logActivityAction)
    {
        $action = $logActivityAction->activityAction->getAction();
        static $end_time = 0;
        $dialogType = null;
        if($logActivityAction->activityAction->dialog !== null) {
            $dialogType = $logActivityAction->activityAction->dialog->dialogSubtype->title;
        }

        $diff = ($end_time === 0)?'-':strtotime($logActivityAction->start_time) - strtotime($end_time);
        $end_time = $logActivityAction->end_time;
        return [
            $logActivityAction->start_time,
            $logActivityAction->end_time,
            $logActivityAction->activityAction->leg_type,
            $action ? $action->getCode() : '',
            $logActivityAction->activityAction->activity->code,
            $logActivityAction->activityAction->activity->category->code,
            $diff,
            strtotime($logActivityAction->end_time) - strtotime($logActivityAction->start_time),
            $dialogType
        ];
    }

    /**
     * @param $logActivityAction
     * @return string
     */
    public function getRowId($logActivityAction)
    {
        return '';
    }
}
/**
 * @}
 */