<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventsResults
 *
 * @property string title
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsResults extends CActiveRecord{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'events_results';
    }
}

?>
