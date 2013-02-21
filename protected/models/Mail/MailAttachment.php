<?php



/**
 * Вложения писем в рамках конкретной симуляции.
 *
 * Связана с моделями: MyDocumentsModel, MailBoxModel.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailAttachment extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_box.id
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
     * @param type $className
     * @return MailAttachment
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
            return 'mail_attachments';
    }
    
    /**
     * Выбрать по заданному письму
     * @param int $mailId
     * @return MailAttachment
     */
    public function byMailId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному набору писем
     * @param array $mailIds
     * @return MailAttachment
     */
    public function byMailIds($mailIds)
    {
        $ids = implode(',', $mailIds);
        
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id in ({$ids})"
        ));
        return $this;
    }
}


