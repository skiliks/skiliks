<?php

/**
 * Содержит список событий, которые есть в системе.
 *
 * @property integer $id
 * @property string  $code, 'ET1.1', 'E8' ...
 * @property string  $title
 * @property integer $on_hold_logic, похоже что это поле сейчас нигде не используется
 * @property integer $on_ignore_result, похоже что это поле сейчас нигде не используется
 * @property string  $trigger_time, game minutes, time when event must be scheduled in game during sim start
 * @property string  $import_id
 */
class EventSample extends CActiveRecord
{
    /**
     * @return int
     */
    public function isMail()
    {
        return preg_match("/M\w+/", $this->code);
    }

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
     * @param string $className
     * @return EventSample
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
        return 'event_sample';
    }
}
