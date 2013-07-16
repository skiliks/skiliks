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
            'Code', 'Text', 'Duration', 'Game time'
        ];
    }

    protected function getRow($logMeeting)
    {
        return [
            $logMeeting->meeting->code,
            $logMeeting->meeting->label,
            $logMeeting->meeting->duration,
            $logMeeting->game_time
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