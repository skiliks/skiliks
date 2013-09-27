<?php

/**
 * This is the model class for table "activity_action".
 *
 * The followings are the available columns in table 'activity_action':
 * @property integer $id
 * @property integer $activity_id
 * @property string $leg_type
 * @property integer $dialog_id
 * @property integer $mail_id
 * @property integer $document_id
 * @property integer $meeting_id
 * @property integer $window_id
 *
 * The followings are the available model relations:
 * @property Activity $activity
 * @property Replica $dialog
 * @property MailTemplate $mail
 * @property DocumentTemplate $document
 * @property string $import_id
 * @property Window $window
 * @property Meeting $meeting
 */
class ActivityAction extends CActiveRecord
{
    const LEG_TYPE_WINDOW = 'Window';
    const LEG_TYPE_INBOX = 'Inbox_leg';
    const LEG_TYPE_OUTBOX = 'Outbox_leg';
    const LEG_TYPE_DOCUMENTS = 'Documents_leg';
    const LEG_TYPE_MANUAL_DIAL = 'Manual_dial_leg';
    const LEG_TYPE_SYSTEM_DIAL = 'System_dial_leg';
    const LEG_TYPE_MEETING = 'Meeting';

    public function isPlan()
    {
        return (null === $this->document_id && null === $this->mail_id && null === $this->dialog_id);
    }

    /**
     * @param $log LogDialog|LogDocument|LogMail|LogMeeting|LogWindow
     */
    public function appendLog($log)
    {
        // get log_action {
        $log_search_criteria = new CDbCriteria();
        $log_search_criteria->addColumnCondition([
            'sim_id' => $log->simulation->id
        ]);
        $log_search_criteria->compare('start_time', $log->start_time);
        $log_search_criteria->compare('window_uid', $log->window_uid);
        /** @var $log_action LogActivityAction */
        $log_action = LogActivityAction::model()->find($log_search_criteria);
        // get log_action }
        // init log_action if not exists {
        if (!$log_action) {
            $log_action = new LogActivityAction();
            $log_action->start_time = $log->start_time;
            $log_action->sim_id = $log->sim_id;
            if (isset($log->file_id)) {
                $log_action->document_id = $log->file_id;
            }
            if (isset($log->window)) {
                $log_action->window = $log->window;
            }

        }
        // init log_action if not exists }
        // We do not decrease activity priority {
        if ($log_action->activity_action_id !== null &&
            $log_action->activityAction->activity->category->priority < $this->activity->category->priority
        ) {
            return;
        }
        // }

        // add window_uid. Currently I`m not sure that window_uid exists in ely log (e.g. DocumentLog)
        if (isset($log->window_uid)) {
            $log_action->window_uid = $log->window_uid;
        }

        # Drafts
        if (isset($log->mail_id)) {
            $activity = ActivityAction::model()->findByPriority(['mail_id' => null], ['Inbox_leg', 'Outbox_leg'], $log->simulation);
            $log_items = LogActivityAction::model()->findAllByAttributes(array(
                'activity_action_id' => $activity->primaryKey,
                'sim_id' => $log->sim_id
            ));
            foreach ($log_items as $log_item) {

                $log_item->mail_id = $log->mail_id;
                $log_item->save();
            }
        }

        $log_action->activity_action_id = $this->id;

        if (isset($log->mail_id)) {
            $log_action->mail_id = $log->mail_id;
        }
        if (isset($log->meeting_id)) {
            $log_action->meeting_id = $log->meeting_id;
        }
        if ($log->end_time!==null && $log->end_time !== '00:00:00') {
            $log_action->end_time = $log->end_time;
        }
        ;
        $log_action->save();

        // update chain of new mail window logs, with same window_uid {
        if ($log instanceof LogMail && LogHelper::MAIL_NEW_WINDOW_TYPE_ID == $log->window) {
            $activityActionLogs = LogActivityAction::model()->findAllByAttributes([
                'sim_id' => $log->simulation->id,
                'window_uid' => $log->window_uid
            ]);
            foreach ($activityActionLogs as $activityActionLog) {
                $activityActionLog->mail_id = $log->mail_id;
                //$activityActionLog->activity_action_id = $this->id;
                $activityActionLog->save();
            }
        }
        // update chain of new mail window logs, with same window_uid }
    }

    /**
     * Returns action
     * @return IGameAction|null
     */
    public function getAction()
    {
        if ($this->mail !== null) {
            return $this->mail;
        } else if ($this->window !== null) {
            return $this->window;
        } else if ($this->dialog !== null) {
            return $this->dialog;
        } else if ($this->document !== null) {
            return $this->document;
        } else if ($this->meeting !== null) {
            return $this->meeting;
        } else {
            return new FakeActivityAction($this->activity);
        }
    }

    /**
     * Order by numeric_id
     * @param $attrs mixed
     * @param $legTypes array
     * @param $simulation Simulation
     * @return ActivityAction
     */
    public function findByPriority($attributes, $legTypes, $simulation)
    {
        $criteria = new CDbCriteria();

        $criteria->with = [
            'activity' => [
                'with' => 'category',
                'select' => false,
                'order' => 'category.priority, activity.numeric_id',
                'limit' => 1
            ]
        ];

        $criteria->addColumnCondition($attributes);

        if ($legTypes !== null) {
            $criteria->addInCondition('leg_type', $legTypes);
        }

        // get finished parent actions

        // collect finished parents
        $completedParents = $simulation->completed_parent_activities;

        $parentIds = array_map(
            function ($parent) {
                return $parent->parent_code;
            },
            $completedParents
        );

        // get excluded activities
        $excludedActivitiesCriteria = new CDbCriteria();
        $excludedActivitiesCriteria->addInCondition('parent', $parentIds);
        $excludedActivities = Activity::model()->findAll($excludedActivitiesCriteria);

        $excludedActivityIds = array_map(
            function ($activity) {
                return $activity->primaryKey;
            },
            $excludedActivities
        );

        $criteria->addNotInCondition('activity_id', $excludedActivityIds);
        $result = $this->find($criteria);

        return $result;
    }

    /* ------------------------------------------------------------------------------------------------- */

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ActivityAction the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'activity_action';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('activity_id', 'required'),
            array('dialog_id, mail_id, document_id', 'numerical', 'integerOnly' => true),
            array('activity_id', 'length', 'max' => 255),
            array('import_id', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, activity_id, dialog_id, mail_id, document_id, import_id, window_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'activity' => array(self::BELONGS_TO, 'Activity', 'activity_id'),
            'dialog'   => array(self::BELONGS_TO, 'Replica', 'dialog_id'),
            'mail'     => array(self::BELONGS_TO, 'MailTemplate', 'mail_id'),
            'document' => array(self::BELONGS_TO, 'DocumentTemplate', 'document_id'),
            'meeting'  => array(self::BELONGS_TO, 'Meeting', 'meeting_id'),
            'window'   => array(self::BELONGS_TO, 'Window', 'window_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id'          => 'ID',
            'activity_id' => 'Activity',
            'dialog_id'   => 'Replica',
            'mail_id'     => 'Mail',
            'document_id' => 'Document',
            'meeting_id'  => 'Meeting',
            'window_id'   => 'Window'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('activity_id', $this->activity_id);
        $criteria->compare('dialog_id', $this->dialog_id);
        $criteria->compare('mail_id', $this->mail_id);
        $criteria->compare('meeting_id', $this->meeting_id);
        $criteria->compare('document_id', $this->document_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }


}