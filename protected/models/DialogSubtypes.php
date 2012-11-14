<?php


/**
 * Содержит типы диалогов.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogSubtypes extends CActiveRecord{
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

?>
