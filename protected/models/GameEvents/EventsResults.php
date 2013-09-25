<?php



/**
 * Возможно устаревшее. В моей логике оно не используется сейчас.
 *
 * @property string title
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsResults extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $title;
    
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
            return 'events_results';
    }
}


