<?php
/**
 * Модель диалогов. Хранит реплики диалогов и связь диалогов с событиями.
 *
 * @property integer $id
 * @property integer $ch_from, characters.id
 * @property integer $ch_to, characters.id
 * @property integer $dialog_subtype, dialog_subtypes
 * @property string  $text
 * @property string  $code, dialog code 'ET1.1', 'E8' ...
 * @property string  $next_event_code, 'ET1.1', 'D1', 'P1', 'M1', 'MS2'
 * @property string  $next_event
 * @property string  $media_file_name
 * @property string  $media_type
 * @property string  $type_of_init, Replica initialization type: dialog, icon, time, flex etc.
 * @property string  $flag_to_switch
 * @property string  $flag_to_switch_2
 * @property integer $event_result - remove it!
 * @property integer $step_number, step in dialog
 * @property integer $replica_number, number of replica in dialog step
 * @property integer $delay
 * @property integer $excel_id
 * @property boolean $fantastic_result
 * @property boolean $is_final_replica
 * @property boolean $demo
 * @property integer $duration
 *
 * @property Character from_character
 * @property Character to_character
 * @property ActivityParent[] termination_parent_actions
 * @property DialogSubtype $dialogSubtype
 *
 */
class Replica extends CActiveRecord implements IGameAction
{
    /**
     * Probably we need aliases for dialog_subtype instead of ids
     *
     * @todo; fix this dirty trick
     *
     * @return bool
     */
    public function isEvent()
    {
        return (1 === (int)$this->dialog_subtype || 5 === (int)$this->dialog_subtype);
    }

    /**
     * @return bool
     */
    public function isPhoneCall()
    {
        return (1 === (int)$this->dialog_subtype);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
     * @param type $className
     * @return Replica
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
        return 'replica';
    }

    public function relations()
    {
        return [
            'from_character'             => [self::BELONGS_TO, 'Character', 'ch_from'],
            'to_character'               => [self::BELONGS_TO, 'Character', 'ch_to'],
            'dialogSubtype'               => [self::BELONGS_TO, 'DialogSubtype', 'dialog_subtype'],
            'termination_parent_actions' => [self::HAS_MANY, 'ActivityParent', 'dialog_id']
        ];
    }
}


