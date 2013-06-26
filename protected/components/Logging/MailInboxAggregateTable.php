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
            $mail->code,
            $mail->folder->name,
            ((int)$mail->readed === 1)?'Да':'Нет',
            ((int)$mail->plan === 1)?'Да':'Нет',
            ((int)$mail->reply === 1)?'Да':'Нет',
            '',
            ''
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
