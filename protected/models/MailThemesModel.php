<?php


/**
 * Набор тем для писем
 * 
 * Связана с моделями:  Simulations.
 *
 * @property string name
 * @property int sim_id
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailThemesModel extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;    
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailThemesModel 
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
            return 'mail_themes';
    }
    
    /**
     * Выборка по набору тем
     * @param array $ids
     * @return MailThemesModel 
     */
    public function byIds($ids)
    {
        $ids = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }
    
    /**
     * Выбрать по имени темы
     * @param string $name
     * @return MailThemesModel 
     */
    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "name = '{$name}'"
        ));
        return $this;
    }
    
    /**
     * Выбрать по идентификатору темы
     * @param int $id
     * @return MailThemesModel 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }


    /**
     * @param int $id, mail_themes_id
     * 
     * @return string od null
     */
    public function getSubjectId($id, $messageId)
    {
        $emailSubject = MailThemesModel::model()->findByPk($id);
        $id = isset($emailSubject) ? $emailSubject->id : null;
        
        if (null === $id) {
            $forvarderEmail = MailBoxModel::model()->findByPk($messageId);
            if (null !== $forvarderEmail) {
                $id = (null === $forvarderEmail->subject_id) ? null : $forvarderEmail->subject_id;
            }
        }
        
        return $id;
    }

}
