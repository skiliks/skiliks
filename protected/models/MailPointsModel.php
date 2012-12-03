<?php



/**
 * Содержит значения оценки для конкретного письма. 
 * Наполняется из импорта оценок по письму
 * 
 * Связана с моделями: MailTemplateModel, CharactersPointsTitles.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailPointsModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailPointsModel 
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
     * @return MailPointsModel 
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
     * @return MailPointsModel 
     */
    public function byPointId($pointId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "point_id = {$pointId}"
        ));
        return $this;
    }
}


