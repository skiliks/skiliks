<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventsChoises
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsChoices extends CActiveRecord{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'events_choices';
    }
}

?>
