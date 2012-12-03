<?php



/**
 * Содержит набор получателей к конкретному письму в рамках симуляции.
 *
 * Связана с моделями:  MailBoxModel, Characters.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailReceiversModel extends CActiveRecord{
    
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
            'condition' => "mail_id = {$id}"
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
}


