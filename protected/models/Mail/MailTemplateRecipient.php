<?php



/**
 * Шаблон набора получателей к письмам
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailTemplateRecipient extends CActiveRecord
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
     * @return MailRecipient
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
            return 'mail_receivers_template';
    }
    
    /**
     * Выбрать по заданному письму
     * @param int $id
     * @return MailTemplateRecipient
     */
    public function byMailId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному получателю
     * @param int $id
     * @return MailTemplateRecipient
     */
    public function byReceiverId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "receiver_id = {$id}"
        ));
        return $this;
    }
}


