<?php


/**
 * Содержит типы диалогов.
 *
 * @parameter string $slug
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogSubtype extends CActiveRecord
{
    const SLUG_CALL        = 'call';
    const SLUG_PHONE_TALK  = 'phone_talk';
    const SLUG_VISIT       = 'visit';
    const SLUG_MEETING     = 'meeting';
    const SLUG_KNOCK_KNOCK = 'knock_knock';

    /**
     * @var integer
     */
    public $id;
    
    /**
     * dialog_type.id
     * @var int
     */
    public $type_id ;   
    
    /**
     * @var string
     */
    public $title;

    /**
     * @return bool
     */
    public function isMeeting()
    {
        return (self::SLUG_MEETING == $this->slug || self::SLUG_KNOCK_KNOCK == $this->slug);
    }

    /**
     * @return bool
     */
    public function isPhoneCall()
    {
        return (self::SLUG_CALL == $this->slug || self::SLUG_PHONE_TALK == $this->slug);
    }
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'dialog_subtypes';
    }
}


