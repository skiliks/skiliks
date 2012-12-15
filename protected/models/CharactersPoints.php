<?php



/**
 * Хранит значение оценки поведения для конкретного диалога.
 * 
 * Связана с моделями: CharactersPointsTitles, Dialogs
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPoints extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * dialogs.id
     * @var integer
     */
    public $dialog_id;
    
    /**
     * characters_points.id
     * @var integer
     */
    public $piont_id;
    
    /**
     * @var integer
     */
    public $add_value;
    
    /* ----------------------------------- */
    
    /**
     *
     * @param type $className
     * @return CharactersPoints 
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
            return 'characters_points';
    }
    
    /**
     * Выборка оценки по конкретному диалогу
     * @param int $dialogId идентификатор диалога
     * @return CharactersPoints 
     */
    public function byDialog($dialogId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "dialog_id = '{$dialogId}'"
        ));
        return $this;
    }
    
    /**
     * Выборка по идентификатору оценки
     * @param int $pointId
     * @return CharactersPoints 
     */
    public function byPoint($pointId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "point_id = '{$pointId}'"
        ));
        return $this;
    }
}


