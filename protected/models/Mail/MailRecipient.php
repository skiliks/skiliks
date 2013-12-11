<?php
/**
 * Содержит набор получателей к конкретному письму в рамках симуляции.
 *
 * @property integer  $id
 * @property integer  $receiver_id, characters.id
 * @property integer  $mail_id, mail_box.id
 *
 * @property Character $receiver
 */
class MailRecipient extends CActiveRecord
{
    
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
        return 'mail_receivers';
    }

    public function relations()
    {
        return [
            'receiver' => [self::BELONGS_TO, 'Character', 'receiver_id'],
        ];
    }
}
