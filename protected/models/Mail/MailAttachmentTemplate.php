<?php

/**
 * Шаблоны вложений писем.
 *
 * @property integer $id
 * @property integer $mail_id, mail_template.id
 * @property integer $file_id, document_template.id
 *
 */
class MailAttachmentTemplate extends CActiveRecord
{
    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     * @param string $className
     * @return MailAttachmentTemplate
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'mail_attachments_template';
    }

    public function relations()
    {
        return [
            'file' => [self::BELONGS_TO, 'DocumentTemplate', 'file_id']
        ];
    }
}
