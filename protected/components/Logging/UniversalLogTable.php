<?php
namespace application\components\Logging;
/**
 * \addtogroup Logging
 * @{
 */
/**
 * Universal log
 */
class UniversalLogTable extends LogTable
{
    public function getHeaders()
    {
        return ['window ' ,'start time', 'end time', 'window uid', 'replica', 'mail', 'document', 'meeting'];
    }

    public function getTitle()
    {
        return 'Universal Log Dev';
    }

    public function getId()
    {
        return 'universal-log-dev';
    }

    /**
     * Процессит строки
     * @param $row \UniversalLog
     * @return array
     */
    protected function getRow($row)
    {

        return [
            $row->window->subtype,
            $row->start_time,
            $row->end_time,
            $row->window_uid,
            ($row->replica === null)?'-':$row->replica->code,
            ($row->mail === null)?'-':(empty($row->mail->code))?'NOT MS':$row->mail->code,
            ($row->file === null)?'-':(empty($row->file->template->code))?'-':$row->file->template->code,
            ($row->meeting === null)?'-':(empty($row->meeting->code))?'-':$row->meeting->code
        ];

    }

    /**
     * @param $logMail
     * @return string
     */
    public function getRowId($row)
    {
        return sprintf(
            'universal-log-screen-%s universal-log-sub-screen-%s universal-log-screen-and-sub-screen-%s-%s ',
            $row[0],
            $row[1],
            $row[0],
            $row[1]
        );
    }


}
