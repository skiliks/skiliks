<?php



/**
 * Содержит набор фраз, которые образуют конкретное письмо, которое было 
 * отправлено польщователем в симуляции.
 *
 * @property int mail_id
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailMessage extends CActiveRecord
{
    public $id;
    
    public $mail_id;
    
    public $phrase_id;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailMessage
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
    
    /**
     * Выбрать по заданному письму
     * @param int $mailId
     * @return MailMessage
     */
    public function byMail($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
}


