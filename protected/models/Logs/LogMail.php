<?php
/**
 * @property integer id
 * @property integer sim_id
 * @property integer mail_id
 * @property integer window
 * @property datetime start_time
 * @property datetime end_time
 * @property Simulation simulation
 * @property Window window_obj
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
    public $full_coincidence;
    
    /**
     * @var string, '-' or mail_template.code
     */
    public $part1_coincidence;
    
    /**
     * @var string, '-' or mail_template.code
     */    
    public $part2_coincidence;
    
    /**
     * @var bool
     */
    public $is_coincidence;

    /**
     * windows unique ID - currently used to determine several mail new windows
     * @var string, md5
     */
    public $window_uid;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return
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
        /** @var $template MailTemplate|null */
        if ($this->full_coincidence !== null && $this->full_coincidence !== '-') {
            $template = MailTemplate::model()->findByAttributes(['code' => $this->full_coincidence]);
        } else {
            $template = (null !== $this->mail) ? $this->mail->template : null;
        };
        /** @var $activity_action ActivityAction */
        $activity_action = null;
        if ($template !== null){
            // If mail is correct MS
            $activity_action = ActivityAction::model()->findByPriority(
                ['mail_id' => $template->primaryKey ],
                ['Inbox_leg', 'Outbox_leg'],
                $this->simulation
            );

            foreach ($template->termination_parent_actions as $parent_action) {
                if (!$parent_action->isTerminatedInSimulation($this->simulation)) {
                    $parent_action->terminateInSimulation($this->simulation);
                }
            };

        } else {
            // If mail is incorrect MS or not sent
            if ($this->mail !== null) {
                if ($this->mail->isSended()) {
                    $activity_action = ActivityAction::model()->findByPriority(
                        ['activity_id' => 'A_incorrect_send' ],
                        NULL,
                        $this->simulation
                    );
                } else {
                    $activity_action = ActivityAction::model()->findByPriority([
                        'activity_id' => 'A_not_sent'
                    ], NULL, $this->simulation);
                }
            }
        }
        if ($activity_action !== null && $this->end_time !== null) {
            $activity_action->appendLog($this);
        }
        parent::afterSave();
    }

    public function relations()
    {
        return array(
            'mail'       => array(self::BELONGS_TO, 'MailBox', 'mail_id'),
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'window_obj' => array(self::BELONGS_TO, 'Window', 'window'),
        );
    }

    /**
     * @deprecated SQL injection
     * @param $v
     * @return LogMail
     */
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
