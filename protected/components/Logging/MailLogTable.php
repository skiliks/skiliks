<?php
namespace application\components\Logging;

/**
 * \addtogroup Logging
 * @{
 */
class MailLogTable extends LogTable
{
    public function getId()
    {
        return 'mail-log';
    }

    public function getTitle()
    {
        return 'Mail log';
    }

    public function getHeaders()
    {
        return ['Start Time', 'End Time', 'Mail code', 'R/W', 'Window', 'Совпадение', 'В копии'];
    }

    /**
     * @param $logMail \LogMail
     * @return array
     */
    protected function getRow($logMail) {
        return [
            $logMail->start_time,
            $logMail->end_time,
            $logMail->mail ? $logMail->mail->code : '--',
            $logMail->mail ? $logMail->mail->subject_obj->wr : '--',
            $logMail->window_obj->subtype,
            $logMail->mail ? $logMail->mail->coincidence_type : '--',
            $logMail->mail ? implode(',', $logMail->mail->getCopyCharacterCodes()) : '--'
        ];
    }

    /**
     * @param $logMail
     * @return string
     */
    public function getRowId($row)
    {
        return sprintf(
            'full-mail-log-code-%s ',
            $row[2]
        );
    }
}
