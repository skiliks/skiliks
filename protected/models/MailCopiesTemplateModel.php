<?php



/**
 * Шаблон персонажей, которые идут копией к конкретному письму.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailCopiesTemplateModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailCopiesTemplateModel 
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
    
    public function byMailId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
    
    public function byReceiverId($receiverId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "receiver_id = {$receiverId}"
        ));
        return $this;
    }
}

?>
