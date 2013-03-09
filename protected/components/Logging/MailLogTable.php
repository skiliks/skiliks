<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 08.03.13
 * Time: 8:03
 * To change this template use File | Settings | File Templates.
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
     * @param $logMail LogMail
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