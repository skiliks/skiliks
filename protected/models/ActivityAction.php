<?php

/**
 * This is the model class for table "activity_action".
 *
 * The followings are the available columns in table 'activity_action':
 * @property integer $id
 * @property string $activity_id
 * @property integer $dialog_id
 * @property integer $mail_id
 * @property integer $document_id
 *
 * The followings are the available model relations:
 * @property Activity $activity
 * @property Dialogs $dialog
 * @property MailTemplateModel $mail
 * @property MyDocumentsTemplateModel $document
 * @property string import_id
 * @property string leg_type
 */
class ActivityAction extends CActiveRecord
{
    
    /**
     * @var bool
     */
    public $is_keep_last_category;
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ActivityAction the static model class
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
            array('dialog_id, mail_id, document_id', 'numerical', 'integerOnly'=>true),
            array('activity_id', 'length', 'max'=>255),
            array('import_id', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, activity_id, dialog_id, mail_id, document_id, import_id, window_id', 'safe', 'on'=>'search'),
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
            'dialog' => array(self::BELONGS_TO, 'Dialogs', 'dialog_id'),
            'mail' => array(self::BELONGS_TO, 'MailTemplateModel', 'mail_id'),
            'document' => array(self::BELONGS_TO, 'MyDocumentsTemplateModel', 'document_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'activity_id' => 'Activity',
            'dialog_id' => 'Dialog',
            'mail_id' => 'Mail',
            'document_id' => 'Document',
            'window_id' => 'Window'
        );
    }

    public function appendLog($log) {
        // get log_action {
        $log_search_criteria = new CDbCriteria();
        $log_search_criteria->addColumnCondition([
          'sim_id' => $log->simulation->id
        ]);
        $log_search_criteria->addCondition('start_time = :start_time');
        $log_search_criteria->params['start_time'] = $log->start_time;
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

        // add window_uid. Currently I`m not sure that window_uid exists in ely log (e.g. DocumentLog)
        if (isset($log->window_uid)) {
            $log_action->window_uid = $log->window_uid;
        }

        # Drafts
        if (isset($log->mail_id)) {
            $activity = ActivityAction::model()->findByPriority(['mail_id' => null], ['Inbox_leg', 'Outbox_leg']);
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
        if ($log->end_time !== '00:00:00') {
            $log_action->end_time = $log->end_time;
        };
        $log_action->save();

        // update chain of new mail window logs, with same window_uid {
        if ($log instanceof LogMail && LogHelper::MAIL_NEW_WINDOW_TYPE_ID == $log->window) {
            $activityActionLogs = LogActivityAction::model()->findAllByAttributes([
                'sim_id'     => $log->simulation->id,
                'window_uid' => $log->window_uid
            ]);
            foreach ($activityActionLogs as $activityActionLog) {
                $activityActionLog->mail_id            = $log->mail_id;
                $activityActionLog->activity_action_id = $this->id;
                $activityActionLog->save();
            }
        }
        // update chain of new mail window logs, with same window_uid }
    }

    /**
     * Order by numeric_id
     */
    public function findByPriority($attrs, $leg_types = null) {
        $criteria = new CDbCriteria();
        $criteria->with = [
            'activity' => [
                'select' => false, 'order' => 'numeric_id', 'limit' => 1
            ]
        ];
        $criteria->addColumnCondition($attrs);
        if ($leg_types !== null) {
            $criteria->addInCondition('leg_type', $leg_types);
        }
        $result = $this->find($criteria);
        return $result;
    }


    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('activity_id',$this->activity_id,true);
        $criteria->compare('dialog_id',$this->dialog_id);
        $criteria->compare('mail_id',$this->mail_id);
        $criteria->compare('document_id',$this->document_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}