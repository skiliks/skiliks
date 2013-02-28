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
    public static function refresh($mailId, $fileId) {
        $model = MailAttachment::model()->byMailId($mailId)->find();
        if ($model) {
            if ($fileId == 0) {
                // удаляем файл
                return $model->delete();
            }
            else {
                $model->file_id = $fileId;
                return $model->update();
            }
        }
        
        if ($fileId == 0) return false;
        
        $model = new MailAttachment();
        $model->mail_id = $mailId;
        $model->file_id = $fileId;
        return $model->insert();
    }
    
    /**
     * Получение информации о вложениях
     * @param int $mailId 
     */
    public static function get($mailId) {
        $model = MailAttachment::model()->byMailId($mailId)->find();
        if (!$model) return false;
        
        $fileId = $model->file_id;
        
        $file = MyDocument::model()->byId($fileId)->find();
        if (!$file) return false;
        
        return array(
            'id' => $fileId,
            'name' => $file->fileName
        );
    }
}


