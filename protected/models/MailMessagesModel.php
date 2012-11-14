<?php



/**
 * Содержит набор фраз, которые образуют конкретное письмо, которое было 
 * отправлено польщователем в симуляции.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailMessagesModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailMessagesModel 
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
            return 'mail_messages';
    }
    
    public function byMail($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
}

?>
