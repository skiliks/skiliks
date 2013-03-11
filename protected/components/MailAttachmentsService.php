<?php



/**
 * Description of MailAttachmentsService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailAttachmentsService {

    /**
     * Обновить состояние вложения к письму
     * @param type $mailId
     * @param type $fileId
     * @return type 
     */
    public static function refresh($mail, $fileId)
    {
        $attachment = MailAttachment::model()->findByAttributes([
            'mail_id' => $mail->id
        ]);
        if (null !== $attachment) {
            if ($fileId == 0) {
                // удаляем файл
                return $attachment->delete();
            }
            else {
                $attachment->file_id = $fileId;
                return $attachment->update();
            }
        }
        
        if ($fileId == 0) {
            return false;
        }

        $attachment2 = new MailAttachment();
        $attachment2->mail_id = $mail->id;
        $attachment2->file_id = $fileId;

        $attachment2->insert();

        return $attachment2;
    }
    
    /**
     * Получение информации о вложениях
     * @param int $mailId 
     */
    public static function get($mail)
    {
        if (null === $mail) {
            return false;
        }
        
        $file = MyDocument::model()->findByPk($mail->file_id);
        if (null === $file) {
            return false;
        }
        
        return array(
            'id'   => $mail->file_id,
            'name' => $file->fileName
        );
    }
}


