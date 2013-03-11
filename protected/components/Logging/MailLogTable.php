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
        return ['Start Time', 'End Time', 'Mail code', 'Window'];
    }

    /**
     * @param $logMail \LogMail
     * @return array
     */
    protected function getRow($logMail) {
        return [
            $logMail->start_time,
            $logMail->end_time,
            $logMail->mail ? $logMail->mail->code : '',
            $logMail->window_obj->subtype
        ];
    }
}
/**
 * @}
 */