<?php
/**
 * @property integer id
 * @property integer sim_id
 * @property integer mail_id
 * @property integer window
 * @property datetime start_time
 * @property datetime end_time
 * @property Simulations simulation
 */
class LogMail extends CActiveRecord
{
    public $id;
    
    public $sim_id;
    
    public $mail_id;
    
    public $window;
    
    public $start_time;
    
    public $end_time;
    
    public $mail_task_id;
    
    /**
     * @var string, '-' or mail_template.code
     */
    public $full_coinsidence;
    
    /**
     * @var string, '-' or mail_template.code
     */
    public $part1_coinsidence;
    
    /**
     * @var string, '-' or mail_template.code
     */    
    public $part2_coinsidence;
    
    /**
     * @var bool
     */
    public $is_coinsidence;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return Characters
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function bySimId($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    public function orderByWindow($sort = 'ACS')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "window $sort"
        ));
        return $this;
    }    
    
    public function orderById($sort = 'ACS')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "id $sort"
        ));
        return $this;
    }    
    
    public function byMailBoxId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
    
    public function byEndTimeGreaterThen($date)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "end_time > '{$date}'"
        ));
        return $this;
    }
    
    public function byMailTaskId($mailTaskId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_task_id = {$mailTaskId}"
        ));
        return $this;
    }

    protected function afterSave()
    {
        if ($this->full_coinsidence !== null && $this->full_coinsidence !== '-') {
            $template = MailTemplateModel::model()->findByAttributes(['code' => $this->full_coinsidence]);
        } else {
            $template = (null !== $this->mail) ? $this->mail->template : null;
        };
        if (null !== $template) {
            $activity_action = ActivityAction::model()->findByPriority(array('mail_id' => $template->primaryKey));
            if ($activity_action !== null) {
                $activity_action->appendLog($this);
            }
        }
        parent::afterSave();
    }

    public function relations()
    {
        return array(
            'mail' => array(self::BELONGS_TO, 'MailBoxModel', 'mail_id'),
            'simulation' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
        );
    }

    public function byWindow($v)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "window = {$v}"
        ));
        return $this;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'log_mail';
    }
}
