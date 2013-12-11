<?php
/**
 * Шаблон набора получателей к письмам
 *
 * @property integer $id
 * @property integer $mail_id, mail_template.id
 * @property integer $receiver_id, characters.id
 */
class MailTemplateRecipient extends CActiveRecord
{
    /** ------------------------------------------------------------------------------------------------------------ **/
     
    /**
     * @param string $className
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
}


