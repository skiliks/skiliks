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
        $model = MailAttachmentsModel::model()->byMailId($mailId)->find();
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
        
        $model = new MailAttachmentsModel();
        $model->mail_id = $mailId;
        $model->file_id = $fileId;
        return $model->insert();
    }
}

?>
