<?php
/**
 * Содержит набор персонажей, которые идут в копии к заданному письму
 *
 * @property integer $id
 * @property integer $mail_id, mail_box.id
 * @property integer $receiver_id, characters.id
 *
 * @property Character $recipient
 */
class MailCopy extends CActiveRecord
{
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @param string $className
     * @return MailCopy
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
        return 'mail_copies';
    }

    public function relations() {
        return array(
            'mail'      => array(self::BELONGS_TO, 'MailBox'  , 'mail_id'),
            'recipient' => array(self::BELONGS_TO, 'Character', 'receiver_id'),
        );
    }
}


