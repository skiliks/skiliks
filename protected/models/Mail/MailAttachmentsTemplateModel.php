<?php



/**
 * Шаблоны вложений писем.
 *
 * Связана с моделями: MyDocumentTemplate, MailTemplateModel.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailAttachmentsTemplateModel extends CActiveRecord
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
     * @return MailAttachmentsTemplateModel
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
     * @return MailAttachmentsTemplateModel 
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
     * @return MailAttachmentsTemplateModel 
     */
    public function byFileId($fileId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "file_id = {$fileId}"
        ));
        return $this;
    }
}


