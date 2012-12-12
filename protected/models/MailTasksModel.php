<?php


/**
 * Набор задач для писем.
 *
 * Связана с моделями:  MailTemplateModel
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailTasksModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailTasksModel
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
            return 'mail_tasks';
    }
    
    /**
     * Выбрать по заданному письму
     * 
     *  MAil id means mailTemplateId
     * 
     * @param type $mailId
     * @return MailTasksModel 
     */
    public function byMailId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
    
    /**
     * @param string $value, 'W', 'R' or 'M'. Wrong, Right, Miscelaniouse
     * @return MailTasksModel 
     */
    public function byWrongRight($value)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "wr = '{$value}'"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному письму
     * 
     *  MAil id means mailTemplateId
     * 
     * @param array of integer $mailId
     * @return MailTasksModel 
     */
    public function byMailIds($mailIds)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => sprintf(
                "mail_id IN (%s)",
                implode(', ', $mailIds)
             )
        ));
        return $this;
    }    
    
    /**
     * Выбрать по заданному идентификатору
     * @param int $id
     * @return MailTasksModel 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
}


