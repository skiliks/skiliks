<?php
namespace application\components\Logging;

/**
 * \addtogroup Logging
 * @{
 */

/**
 * Class MeetingLogTable
 * @package application\components\Logging
 */
class MeetingLogTable extends LogTable
{
    public function getId()
    {
        return 'meeting-log';
    }

    public function getTitle()
    {
        return 'Meeting log';
    }

    public function getHeaders()
    {
        return [
            'Code', 'Text', 'Duration', 'Start time', 'End time'
        ];
    }

    protected function getRow($logMeeting)
    {
        return [
            $logMeeting->meeting->code,
            $logMeeting->meeting->icon_text,
            $logMeeting->meeting->duration,
            $logMeeting->start_time,
            $logMeeting->end_time
        ];
    }

    /**
     * @param $logDialog
     * @return string
     */
    public function getRowId($logDialog)
    {
        return '';
    }
}
/**
 * @}
 */