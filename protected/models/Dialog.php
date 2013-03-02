<?php
/**
 * @property string $code
 * @property string $title
 * @property string $type
 * @property string $category
 * @property string $start_by, 'time'/'dialog'
 * @property string start_time, '00:00:00'
 * @property integer delay, in game minutes
 * @property boolean is_use_in_demo
 * @property string import_id
 */
class Dialog extends CActiveRecord
{
    const START_BY_DIALOG = 'dialog';
    const START_BY_TIME   = 'time';

    const TYPE_VISIT      = 'visit';
    const TYPE_PHONE_CALL = 'phone_call';
    const TYPE_PHONE_TALK = 'phone_talk';
    const TYPE_KNOCK      = 'knock_knock';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Flag the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function setTypeFromExcel($string) {
        switch ($string) {
            case 'Встреча' :              $this->type = self::TYPE_VISIT; break;
            case 'Звонок' :               $this->type = self::TYPE_PHONE_CALL; break;
            case 'Разговор по телефону' : $this->type = self::TYPE_PHONE_TALK; break;
            case 'Стук в дверь' :         $this->type = self::TYPE_KNOCK; break;
        }
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'dialogs';
    }

    public function primaryKey() {
        return array('code');
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [];
    }

}
