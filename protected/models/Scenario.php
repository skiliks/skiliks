<?php

/**
 * This is the model class for table "scenario".
 *
 * The followings are the available columns in table 'scenario':
 * @property integer $id
 * @property string $name
 * @property string $filename
 * @property string $slug
 * @property string $start_time
 * @property string $end_time
 * @property string $finish_time
 * @property int $duration_in_game_min
 *
 * The followings are the available model relations:
 * @property Activity[] $activities
 * @property ActivityAction[] $activityActions
 * @property ActivityParent[] $activityParents
 * @property Character[] $characters
 * @property CharactersPoint[] $charactersPoints
 * @property Dialog[] $dialogs
 * @property EventSample[] $eventSamples
 * @property Flag[] $flags
 * @property FlagBlockDialog[] $flagBlockDialogs
 * @property FlagBlockMail[] $flagBlockMails
 * @property FlagBlockReplica[] $flagBlockReplicas
 * @property FlagRunEmail[] $flagRunEmails
 * @property HeroBehaviour[] $heroBehaviours
 * @property LearningArea[] $learningAreas
 * @property LearningGoal[] $learningGoals
 * @property MailAttachmentsTemplate[] $mailAttachmentsTemplates
 * @property MailConstructor[] $mailConstructors
 * @property MailCopiesTemplate[] $mailCopiesTemplates
 * @property MailPhrase[] $mailPhrases
 * @property MailPoint[] $mailPoints
 * @property MailTask[] $mailTasks
 * @property MailTemplate[] $mailTemplates
 * @property MyDocumentsTemplate[] $myDocumentsTemplates
 * @property PerformanceRule[] $performanceRules
 * @property PerformanceRuleCondition[] $performanceRuleConditions
 * @property Replica[] $replicas
 * @property Simulation[] $simulations
 * @property Task[] $tasks
 * @property ScenarioConfig $scenario_config
 *
 * @method Replica getReplica
 * @method Theme getTheme
 * @method Activity getActivity
 * @method ActivityAction getActivityAction
 * @method ActivityParentAvailability getActivityParentAvailability
 * @method OutgoingPhoneTheme getOutgoingPhoneTheme
 * @method OutboxMailTheme getOutboxMailTheme
 * @method MailConstructor getMailConstructor
 * @method MailTemplate getMailTemplate
 * @method FlagOutboxMailThemeDependence getFlagOutboxMailThemeDependence
 * @method FlagOutgoingPhoneThemeDependence getFlagOutgoingPhoneThemeDependence
 * @method MailAttachmentTemplate getMailAttachmentTemplate
 * @method Character getCharacter
 * @method Paragraph getParagraph
 * @method ParagraphPocket getParagraphPocket
 */
class Scenario extends CActiveRecord
{
    const TYPE_LITE     = 'lite';
    const TYPE_FULL     = 'full';
    const TYPE_TUTORIAL = 'tutorial';

    public function __call($name, $parameters)
    {
        $matches = [];
        if (preg_match('/get([A-Z].*)$/',$name, $matches)) {
            $relation = $matches[1];
            if (class_exists($relation)) {
                if ($parameters[0] instanceof CDbCriteria) {
                    $parameters[0]->compare('scenario_id', $this->getPrimaryKey());
                    return $relation::model()->find($parameters[0]);
                } else {
                    $parameters[0]['scenario_id'] = $this->getPrimaryKey();
                    return $relation::model()->findByAttributes($parameters[0]);
                }
            }
        }
        return parent::__call($name, $parameters); // TODO: Change the autogenerated stub
    }

    /**
     * @return bool
     */
    public function isLite()
    {
        return $this->slug === self::TYPE_LITE;
    }

    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->slug === self::TYPE_FULL;
    }

    /**
     * @return bool
     */
    public function isAllowOverride() {
        return $this->scenario_config->is_allow_override === 'true';
    }

    /**
     * @return bool
     */
    public function isCalculateAssessment() {
        return $this->scenario_config->is_calculate_assessment === 'yes';
    }

    /**
     * @return string
     */
    public function getSlugCss()
    {
        $arr = [
            self::TYPE_FULL => 'label-inverse',
            self::TYPE_LITE => 'label-info',
            self::TYPE_TUTORIAL => 'label-default',
        ];

        if (isset($arr[$this->slug])) {
            return $arr[$this->slug];
        }

        return '';
    }

    /* ---------------------------------------------------------------------------------------------- */

    /**
     * @param $attributes
     * @return Dialog
     */
    public function getPreformanceRules($attributes)
    {
        $attributes['scenario_id'] = $this->primaryKey;
        return PerformanceRule::model()->findAllByAttributes($attributes);
    }

    /**
     * @param $attributes
     * @return Replica
     */
    public function getReplicaPoints($attributes, $params = [])
    {
        $attributes['scenario_id'] = $this->primaryKey;
        return ReplicaPoint::model()->findAllByAttributes($attributes, $params);
    }

    /**
     * @param $attributes
     * @param $params
     * @return Replica[]
     */
    public function getReplicas($attributes, $params = [])
    {
        if (is_array($attributes)) {
            $attributes['scenario_id'] = $this->primaryKey;
            return Replica::model()->findAllByAttributes($attributes, $params);
        } else if ($attributes instanceof CDbCriteria) {
            $attributes->compare('scenario_id', $this->getPrimaryKey());
            return Replica::model()->findAll($attributes, $params);
        } else {
            assert(false);
        }
    }

    /**
     * @param $data
     * @return Character[]
     */
    public function getCharacters($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return Character::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->getPrimaryKey());
            return Character::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return array of HeroBehaviour
     */
    public function getHeroBehavours($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return HeroBehaviour::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return HeroBehaviour::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return array of LearningGoal
     */
    public function getLearningGoals($data = [])
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return LearningGoal::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return LearningGoal::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return LearningGoalGroup[]
     */
    public function getLearningGoalGroups($data = [])
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return LearningGoalGroup::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return LearningGoalGroup::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return array of LearningArea
     */
    public function getLearningAreas($data = [])
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return LearningArea::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return LearningArea::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return array of LearningGoal
     */
    public function getLearningGoal($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return LearningGoal::model()->findByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return LearningGoal::model()->find($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return array of AssessmentGroup
     */
    public function getAssessmentGroups($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return AssessmentGroup::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return AssessmentGroup::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return array of AssessmentGroup
     */
    public function getAssessmentGroup($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return AssessmentGroup::model()->findByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return AssessmentGroup::model()->find($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return array of StressRule
     */
    public function getStressRule($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return StressRule::model()->findByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return StressRule::model()->find($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return MaxRate
     */
    public function getMaxRate($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return MaxRate::model()->findByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return MaxRate::model()->find($data);
        } else {
            assert(false);
            return null;
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return array of MaxRate
     */
    public function getMaxRates($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return MaxRate::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return MaxRate::model()->findAll($data);
        } else {
            assert(false);
            return [];
        }
    }

    /**
     * @param array of strings | CDbCriteria $data
     *
     * @return Flag
     */
    public function getFlag($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return Flag::model()->findByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return Flag::model()->find($data);
        } else {
            assert(false);
            return null;
        }
    }

    /**
     * @param array of strings $array
     *
     * @return array MailPoint
     */
    public function getMailPoints($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return MailPoint::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return MailPoint::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array $data strings
     *
     * @return array Meeting
     */
    public function getMeetings($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return Meeting::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return Meeting::model()->findAll($data);
        } else {
            assert(false);
            return [];
        }
    }

    /**
     * @param array $data
     *
     * @return OutboxMailTheme[]
     */
    public function getOutboxMailThemes($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return OutboxMailTheme::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return OutboxMailTheme::model()->findAll($data);
        } else {
            assert(false);
            return [];
        }
    }

    /**
     * @param array $data
     *
     * @return OutgoingPhoneTheme[]
     */
    public function getOutgoingPhoneThemes($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return OutgoingPhoneTheme::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return OutgoingPhoneTheme::model()->findAll($data);
        } else {
            assert(false);
            return [];
        }
    }

    /**
     * @param array $data
     *
     * @return FlagOutgoingPhoneThemeDependence[]
     */
    public function getFlagOutgoingPhoneThemeDependencies($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return FlagOutgoingPhoneThemeDependence::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return FlagOutgoingPhoneThemeDependence::model()->findAll($data);
        } else {
            assert(false);
            return [];
        }
    }

    /**
     * @param array $data
     *
     * @return FlagOutboxMailThemeDependence[]
     */
    public function getFlagOutboxMailThemeDependencies($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return FlagOutboxMailThemeDependence::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return FlagOutboxMailThemeDependence::model()->findAll($data);
        } else {
            assert(false);
            return [];
        }
    }

    /**
     * @param array of strings $array
     *
     * @return array ActivityParentAvailability
     */
    public function getActivityParentsAvailability($data = [])
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return ActivityParentAvailability::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->id);
            return ActivityParentAvailability::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    /**
     * @param array $array
     *
     * @return array MailTemplate
     */
    public function getMailTemplates($array)
    {
        $array['scenario_id'] = $this->id;
        return MailTemplate::model()->findAllByAttributes($array);
    }

    /**
     * @param array of strings $array
     *
     * @return array Task
     */
    public function getTasks($array)
    {
        $array['scenario_id'] = $this->id;
        return Task::model()->findAllByAttributes($array);
    }

    /**
     * @param array of strings $array
     *
     * @return array MailTask
     */
    public function getMailTasks($array)
    {
        $array['scenario_id'] = $this->id;
        return MailTask::model()->findAllByAttributes($array);
    }

    /**
     * @param array of strings $array
     *
     * @return array PerformanceRule
     */
    public function getPerformanceRules($array)
    {
        $array['scenario_id'] = $this->id;
        return PerformanceRule::model()->findAllByAttributes($array);
    }

    /**
     * @param array of strings $array
     *
     * @return array FlagBlockReplica
     */
    public function getFlagBlockReplicas($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagBlockReplica::model()->findAllByAttributes($array);
    }

    /**
     * @param array of strings $array
     *
     * @return array DocumentTemplate
     */
    public function getDocumentTemplates($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return DocumentTemplate::model()->findAllByAttributes($array);
    }

    /**
     * @param array of strings $array
     *
     * @return array StressRule
     */
    public function getStressRules()
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return StressRule::model()->findAllByAttributes($array);
    }

    /**
     * @param array of string $array
     * @return Weight|null
     */
    public function getWeight($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return Weight::model()->findByAttributes($array);
    }

    /**
     * @param array of string $array
     * @return FlagRunMail|null
     */
    public function getFlagsRunMail($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagRunMail::model()->findAllByAttributes($array);
    }

    /**
     * @param array of string $array
     * @return FlagAllowMeeting
     */
    public function getFlagAllowMeetings($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagAllowMeeting::model()->findAllByAttributes($array);
    }

    /**
     * @param array of string $array
     * @return FlagSwitchTime
     */
    public function getFlagsSwitchTime($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagSwitchTime::model()->findAllByAttributes($array);
    }

    // --------------------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Scenario the static model class
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
		return 'scenario';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, filename, slug', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, filename, slug, start_time, end_time, finish_time', 'safe', 'on'=>'search'),
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
			'activities' => array(self::HAS_MANY, 'Activity', 'scenario_id'),
			'activityActions' => array(self::HAS_MANY, 'ActivityAction', 'scenario_id'),
			'activityParents' => array(self::HAS_MANY, 'ActivityParent', 'scenario_id'),
			'characters' => array(self::HAS_MANY, 'Characters', 'scenario_id'),
			'charactersPoints' => array(self::HAS_MANY, 'CharactersPoints', 'scenario_id'),
			'dialogs' => array(self::HAS_MANY, 'Dialogs', 'scenario_id'),
			'eventSamples' => array(self::HAS_MANY, 'EventSample', 'scenario_id'),
			'flags' => array(self::HAS_MANY, 'Flag', 'scenario_id'),
			'flagBlockDialogs' => array(self::HAS_MANY, 'FlagBlockDialog', 'scenario_id'),
			'flagBlockMails' => array(self::HAS_MANY, 'FlagBlockMail', 'scenario_id'),
			'flagBlockReplicas' => array(self::HAS_MANY, 'FlagBlockReplica', 'scenario_id'),
			'flagRunEmails' => array(self::HAS_MANY, 'FlagRunEmail', 'scenario_id'),
			'heroBehaviours' => array(self::HAS_MANY, 'HeroBehaviour', 'scenario_id'),
			'learningAreas' => array(self::HAS_MANY, 'LearningArea', 'scenario_id'),
			'learningGoals' => array(self::HAS_MANY, 'LearningGoal', 'scenario_id'),
			'mailAttachmentsTemplates' => array(self::HAS_MANY, 'MailAttachmentsTemplate', 'scenario_id'),
			'mailConstructors' => array(self::HAS_MANY, 'MailConstructor', 'scenario_id'),
			'mailCopiesTemplates' => array(self::HAS_MANY, 'MailCopiesTemplate', 'scenario_id'),
			'mailPhrases' => array(self::HAS_MANY, 'MailPhrases', 'scenario_id'),
			'mailPoints' => array(self::HAS_MANY, 'MailPoints', 'scenario_id'),
			'mailTasks' => array(self::HAS_MANY, 'MailTasks', 'scenario_id'),
			'mailTemplates' => array(self::HAS_MANY, 'MailTemplate', 'scenario_id'),
			'myDocumentsTemplates' => array(self::HAS_MANY, 'MyDocumentsTemplate', 'scenario_id'),
			'performanceRules' => array(self::HAS_MANY, 'PerformanceRule', 'scenario_id'),
			'performanceRuleConditions' => array(self::HAS_MANY, 'PerformanceRuleCondition', 'scenario_id'),
			'replicas' => array(self::HAS_MANY, 'Replica', 'scenario_id'),
			'simulations' => array(self::HAS_MANY, 'Simulations', 'scenario_id'),
			'tasks' => array(self::HAS_MANY, 'Tasks', 'scenario_id'),
            'scenario_config' => array(self::HAS_ONE, 'ScenarioConfig', 'scenario_id'),
            'outboxMailTheme' => array(self::HAS_ONE, 'OutboxMailTheme', 'scenario_id'),
		);
	}


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'filename' => 'Filename',
            'slug' => 'Slug',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('filename',$this->filename,true);
        $criteria->compare('slug',$this->slug,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}