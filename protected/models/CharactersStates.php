<?php



/**
 * Модель состояний, в которых находится персонаж.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersStates extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $title;
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'characters_states';
    }
}


