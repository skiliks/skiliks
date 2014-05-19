<?php
/**
 * Содержит типы диалогов.
 *
 * @property integer $id
 * @property integer $type_id, dialog_types.id
 * @property string  $slug
 * @property string  $title
 *
 */
class DialogSubtype extends CActiveRecord
{
    const SLUG_CALL        = 'call';
    const SLUG_PHONE_TALK  = 'phone_talk';
    const SLUG_VISIT       = 'visit';
    const SLUG_MEETING     = 'meeting';
    const SLUG_KNOCK_KNOCK = 'knock_knock';

    /* --------------------------------------------------------------------------------------------- */

    /**
     * @return bool
     */
    public function isMeeting()
    {
        return in_array($this->slug, [self::SLUG_MEETING, self::SLUG_KNOCK_KNOCK, self::SLUG_VISIT]);
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


