<?php



/**
 * Содержит значения оценки для конкретного письма. 
 * Наполняется из импорта оценок по письму
 *
 * @property mixed import_id
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailPoint extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_template.id
     * @var integer
     */
    public $mail_id;
    
    /**
     * character_points_titles.id
     * @var integer
     */
    public $point_id;   
    
    /**
     * @var integer
     */
    public $add_value;  


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
     * По заданному письму
     * @param int $id
     * @return MailPoint
     */
    public function byMailId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$id}"
        ));
        return $this;
    }
    
    /**
     * По заданной оценке
     * @param int $pointId
     * @return MailPoint
     */
    public function byPointId($pointId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "point_id = {$pointId}"
        ));
        return $this;
    }
    
    /**
     * @param string $ids
     * @return MailTemplate
     */
    public function byIdsNotIn($ids)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " `id` NOT  IN ({$ids})"
        ));
        return $this;
    }    
}


