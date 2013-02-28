<?php


/**
 * Содержит типы диалогов.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogSubtype extends CActiveRecord
{
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


