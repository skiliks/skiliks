<?php


/**
 * Модель персонажей
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Characters extends CActiveRecord{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'characters';
    }
}

?>
