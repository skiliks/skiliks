<?php
/**
 * Содержит набор фраз, которые образуют конкретное письмо, которое было 
 * отправлено польщователем в симуляции.
 *
 * @property integer $id
 * @property integer $mail_id
 * @property integer $phrase_id
 */
class MailMessage extends CActiveRecord
{
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @param string $className
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
}


