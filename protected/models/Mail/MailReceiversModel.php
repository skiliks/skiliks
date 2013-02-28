<?php



/**
 * Содержит набор получателей к конкретному письму в рамках симуляции.
 *
 * @property int receiver_id
 * @property int  mail_id
 */
class MailReceiversModel extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_box.id
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
     * @return MailReceiversModel 
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
            return 'mail_receivers';
    }
    
    /**
     * Выбрать по заданному письму
     * @param int $id
     * @return MailReceiversModel 
     */
    public function byMailId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = :id",
            'params' => ['id' => $id]
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному получателю
     * @param int $id
     * @return MailReceiversModel 
     */
    public function byReceiverId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "receiver_id = {$id}"
        ));
        return $this;
    }
    
    /**
     * @param string $ids
     * @return \MailTemplateModel
     */
    public function byIdsNotIn($ids)
    {
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition('id', explode(',', $ids));
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }  
}


