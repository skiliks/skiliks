<?php
/**
 * Содержит значения оценки для конкретного письма. 
 * Наполняется из импорта оценок по письму
 *
 * @property integer $id
 * @property integer $mail_id, mail_template.id
 * @property integer $point_id, character_points_titles.id
 * @property integer $add_value
 * @property integer $import_id
 */
class MailPoint extends CActiveRecord
{
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailPoint
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
        return 'mail_points';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mail'  => array(self::BELONGS_TO, 'MailTemplate', 'mail_id'),
            'point' => array(self::BELONGS_TO, 'HeroBehaviour', 'point_id'),
        );
    }
}


