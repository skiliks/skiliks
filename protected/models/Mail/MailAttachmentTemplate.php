<?php



/**
 * Шаблоны вложений писем.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailAttachmentTemplate extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_template.id
     * @var int
     */
    public $mail_id;
    
    /**
     * @var int
     */
    public $file_id; // ?
    
    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
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
    
    
    /**
     * Выбрать по заданному письму
     * @param int $mailId
     * @return MailAttachmentTemplate
     */
    public function byMailId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданому файлу
     * @param int $fileId
     * @return MailAttachmentTemplate
     */
    public function byFileId($fileId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "file_id = {$fileId}"
        ));
        return $this;
    }
}


