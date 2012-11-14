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

?>
