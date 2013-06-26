<?php
namespace application\components\Logging;

/**
 * \addtogroup Logging
 * @{
 */
class MailInboxAggregateTable extends LogTable
{
    public function getId()
    {
        return 'mail-inbox-aggregate';
    }

    public function getTitle()
    {
        return 'Mail inbox aggregate log';
    }

    public function getHeaders()
    {
        return ['Код входящего письма', 'Папка мейл-клиента', 'Письмо прочтено (да/нет)', 'Письмо запланировано (да/нет)', 'На письмо отправлен ответ', 'MailTask_id', 'Правильно ли запланирована задача (W/R/N)'];
    }

    /**
     * @param $mail \MailBox
     * @return array
     */
    protected function getRow($mail) {
        return [
            $mail['code'],
            $mail['folder'],
            $mail['readed'],
            $mail['plan'],
            $mail['reply'],
            $mail['task_id'],
            $mail['mail_task_is_correct']
        ];
    }

    /**
     * @param $logMail
     * @return string
     */
    public function getRowId($row)
    {
        return sprintf(
            'inbox-mail-log-agg-%s ',
            $row[2]
        );
    }
}
