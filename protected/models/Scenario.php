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
 *
 * The followings are the available model relations:
 * @property Activity[] $activities
 * @property ActivityAction[] $activityActions
 * @property ActivityParent[] $activityParents
 * @property Character[] $characters
 * @property CharactersPoint[] $charactersPoints
 * @property CommunicationTheme[] $communicationThemes
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
 * @property MailPhrases[] $mailPhrases
 * @property MailPoints[] $mailPoints
 * @property MailTasks[] $mailTasks
 * @property MailTemplate[] $mailTemplates
 * @property MyDocumentsTemplate[] $myDocumentsTemplates
 * @property PerformanceRule[] $performanceRules
 * @property PerformanceRuleCondition[] $performanceRuleConditions
 * @property Replica[] $replicas
 * @property Simulations[] $simulations
 * @property Tasks[] $tasks
 */
class Scenario extends CActiveRecord
{
    const TYPE_LITE = 'lite';
    const TYPE_FULL = 'full';

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
			array('id, name, filename, slug, start_time, end_time', 'safe', 'on'=>'search'),
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
			'communicationThemes' => array(self::HAS_MANY, 'CommunicationThemes', 'scenario_id'),
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
		);
	}

    /**
     * @param $attributes
     * @return Dialog
     */
    public function getDialog($attributes)
    {
        $attributes['scenario_id'] = $this->primaryKey;
        return Dialog::model()->findByAttributes($attributes);
    }

    /**
     * @param $attributes
     * @return Replica
     */
    public function getReplica($attributes, $params = [])
    {
        $attributes['scenario_id'] = $this->primaryKey;
        return Replica::model()->findByAttributes($attributes, $params);
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

    public function getConstructor($array)
    {
        $array['scenario_id'] = $this->id;
        return MailConstructor::model()->findByAttributes($array);
    }

    public function getEventSample($array)
    {
        $array['scenario_id'] = $this->id;
        return EventSample::model()->findByAttributes($array);
    }

    public function getCharacter($array)
    {
        $array['scenario_id'] = $this->id;
        return Character::model()->findByAttributes($array);
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

    public function getMailTemplates($array)
    {
        $array['scenario_id'] = $this->id;
        return MailTemplate::model()->findAllByAttributes($array);
    }

    public function getMailTemplate($array)
    {
        $array['scenario_id'] = $this->id;
        return MailTemplate::model()->findByAttributes($array);
    }

    public function getCommunicationTheme($array)
    {
        $array['scenario_id'] = $this->id;
        return CommunicationTheme::model()->findByAttributes($array);
    }

    public function getCommunicationThemes($data)
    {
        if (is_array($data)) {
            $data['scenario_id'] = $this->id;
            return CommunicationTheme::model()->findAllByAttributes($data);
        } else if ($data instanceof CDbCriteria) {
            $data->compare('scenario_id', $this->getPrimaryKey());
            return CommunicationTheme::model()->findAll($data);
        } else {
            assert(false);
        }
    }

    public function getTask($array)
    {
        $array['scenario_id'] = $this->id;
        return Task::model()->findByAttributes($array);
    }

    public function getDocumentTemplate($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return DocumentTemplate::model()->findByAttributes($array);
    }

    /**
     * @param $array
     * @return LearningArea
     */
    public function getLearningArea($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return LearningArea::model()->findByAttributes($array);
    }

    public function getHeroBehavours($array)
    {
        if (is_array($array)) {
            $data['scenario_id'] = $this->id;
            return HeroBehaviour::model()->findAllByAttributes($data);
        } else if ($array instanceof CDbCriteria) {
            $array->compare('scenario_id', $this->id);
            return HeroBehaviour::model()->findAll($array);
        } else {
            assert(false);
        }
    }

    public function getHeroBehaviour($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return HeroBehaviour::model()->findByAttributes($array);
    }

    public function getTasks($array)
    {
        $array['scenario_id'] = $this->id;
        return Task::model()->findAllByAttributes($array);
    }

    public function getMailTask($array)
    {
        $array['scenario_id'] = $this->id;
        return MailTask::model()->findByAttributes($array);
    }

    public function getMailTasks($array)
    {
        $array['scenario_id'] = $this->id;
        return MailTask::model()->findAllByAttributes($array);
    }

    public function getActivity($array)
    {
        $array['scenario_id'] = $this->id;
        return Activity::model()->findByAttributes($array);
    }

    public function getPerformanceRule($array)
    {
        $array['scenario_id'] = $this->id;
        return PerformanceRule::model()->findByAttributes($array);
    }

    public function getPerformanceRules($array)
    {
        $array['scenario_id'] = $this->id;
        return PerformanceRule::model()->findAllByAttributes($array);
    }

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

    public function getMailPoint($array)
    {
        $array['scenario_id'] = $this->id;
        return MailPoint::model()->findByAttributes($array);
    }

    public function getLearningGoals($data)
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

    public function getDocumentTemplates($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return DocumentTemplate::model()->findAllByAttributes($array);
    }

    public function getFlagBlockMail($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagBlockMail::model()->findByAttributes($array);
    }

    public function getFlagRunMail($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagRunMail::model()->findByAttributes($array);
    }

    public function getFlagBlockDialog($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagBlockDialog::model()->findByAttributes($array);
    }

    public function getFlagBlockReplica($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagBlockReplica::model()->findByAttributes($array);
    }

    public function getFlagBlockReplicas($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagBlockReplica::model()->findAllByAttributes($array);
    }

    public function getFlagsRunMail($array)
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return FlagRunMail::model()->findAllByAttributes($array);
    }

    public function getStressRules()
    {
        $array['scenario_id'] = $this->getPrimaryKey();
        return StressRule::model()->findAllByAttributes($array);
    }
}