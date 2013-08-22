<?php
namespace application\components\Logging;

/**
 * \addtogroup Logging
 * @{
 */
class MailOutboxAggregateTable extends LogTable
{
    public function getId()
    {
        return 'mail-outbox-aggregate';
    }

    public function getTitle()
    {
        return 'Mail outbox aggregate log';
    }

    public function getHeaders()
    {
        return ['id_исходящего письма',
                'Отправлено',
                'Кому',
                'Копия',
                'Тема',
                'Код вложения',
                'Код письма',
                'Степень совпадения',
                'Есть хоть одно совпадение (да/нет)'
        ];
    }

    /**
     * @param $mail \MailBox
     * @return array
     */
    protected function getRow($mail) {

        return [
            $mail->id,
            ($mail->isSended())?'да':'нет',
            $mail->getRecipientsCode(),
            $mail->getCopiesCode(),
            $mail->subject_obj->text,
            (empty($mail->attachment->myDocument->template->code))?'-':$mail->attachment->myDocument->template->code,
            (empty($mail->code))?'-':$mail->code,
            (empty($mail->coincidence_type))?'-':$mail->coincidence_type,
            (!$mail->isHasCoincidence())?'да':'нет'
        ];
    }

    /**
     * @param $logMail
     * @return string
     */
    public function getRowId($row)
    {
        return sprintf(
            'outbox-mail-log-agg-%s ',
            $row[2]
        );
    }
}
