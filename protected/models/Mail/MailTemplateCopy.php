<?php



/**
 * Шаблон персонажей, которые идут копией к конкретному письму.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailTemplateCopy extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_template.id
     * @var integer
     */
    public $mail_id;
    
    /**
     * characters.id
     * @var int
     */
    public $receiver_id;     
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailTemplateCopy
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
            return 'mail_copies_template';
    }
    
    /**
     * Выбрать по заданному письму
     * @param int $mailId
     * @return MailTemplateCopy
     */
    public function byMailId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по получателю
     * @param int $receiverId
     * @return MailTemplateCopy
     */
    public function byReceiverId($receiverId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "receiver_id = {$receiverId}"
        ));
        return $this;
    }
    
    /**
     * @param string $ids
     * @return MailTemplate
     */
    public function byIdsNotIn($ids)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " `id` NOT  IN ({$ids})"
        ));
        return $this;
    }  
}


