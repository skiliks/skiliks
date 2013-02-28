<?php


/**
 * Набор задач для писем.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailTask extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;    
    
    /**
     * mail_box.id
     * @var integer
     */
    public $mail_id;
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * In game minutes
     * @var integer
     */
    public $duration; 
    
    /**
     * Code, 'M1', 'M8' ...
     * @var string
     */
    public $code;  
    
    /**
     * @var string
     */
    public $wr; // right, werong : "R", "W" 
    
    /**
     * @var integer
     */
    public $category; // ?  
    
    /**
     * @var string
     */
    public $import_id;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailTask
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
     * @return MailTask 
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
     * @return MailTask 
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
     * @return MailTask 
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
     * @return MailTask 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * @param string $name
     * @return MailTask 
     */
    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "name = '$name'"
        ));
        return $this;
    }
}


