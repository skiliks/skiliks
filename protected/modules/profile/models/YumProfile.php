<?php

/**
 * Class YumProfile
 *
 * @param string $email
 * @param string $user_id
 * @param string $lastname
 * @param string $firstname
 */

class YumProfile extends YumActiveRecord
{
	const PRIVACY_PRIVATE = 'private';
	const PRIVACY_PUBLIC = 'public';

	/**
	 * @var array of YumProfileFields
	 */
	static $fields = null;

    // --------------------------------------------------------------------------------------------------------

    public function updateFirstNameFromEmail()
    {
        if (0 < strpos($this->email, '@')) {
            $this->firstname = substr($this->email, 0, strpos($this->email, '@'));
        } else {
            $this->firstname = $this->email;
        }
    }

    public function getEmailAlreadyExistMessage()
    {
        return Yii::t('site',  'Email already exists, but not activated.')
            . CHtml::link(
                Yii::t('site','Send activation again'),
                '/activation/resend/' . $this->profile->id
            );
    }

    // --------------------------------------------------------------------------------------------------------

	public function init()
	{
		parent::init();
		// load profile fields only once
		$this->loadProfileFields();
	}

	public function afterSave() {
		if($this->isNewRecord) 
			Yii::log(Yii::t('site',  'A profile been created: {profile}', array(
							'{profile}' => json_encode($this->attributes))));
		else
			Yii::log(Yii::t('site',  'A profile been update: {profile}', array(
							'{profile}' => json_encode($this->attributes))));

		return parent::afterSave();
	}

	public function recentComments($count = 3) {
		$criteria = new CDbCriteria;
		$criteria->condition = 'id = ' .$this->id;
		$criteria->order = 'createtime DESC';
		$criteria->limit = $count;
		return YumProfileComment::model()->findAll($criteria);
	}

	public function beforeValidate() {
		if($this->isNewRecord)
			$this->timestamp = time();
		return parent::beforeValidate();
	}

	/**
	 * @param string $className
	 * @return YumProfile
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	// All fields that the user has activated in his privacy settings will
	// be obtained and returned for the use in the profile view
	public function getPublicFields() {
		if(!Yum::module('profile')->enablePrivacySetting)
			return false;

		$fields = array();

		if($privacy = YumUser::model()
				->cache(500)
				->with('privacy')
				->findByPk($this->user_id)
				->privacy->public_profile_fields) {
			$i = 1;
			foreach(YumProfileField::model()->cache(3600)->findAll() as $field) {
				if(
						(($i & $privacy) 
						 && $field->visible != YumProfileField::VISIBLE_HIDDEN) 
						|| $field->visible == YumProfileField::VISIBLE_PUBLIC)
					$fields[] = $field;
				$i*=2;
			}
		}
		return $fields;
	}

	/**
	 * Returns resolved table name 
	 * @return string
	 */
	public function tableName()
	{
		$this->_tableName = Yum::module('profile')->profileTable;
		return $this->_tableName;
	}

	public function rules()
	{
		$required = array();
		$numerical = array();
		$rules = array();
		$safe = array();

		foreach (self::$fields as $field) {
			$field_rule = array();

			if ($field->required == 1)
				array_push($required, $field->varname);

			if ($field->field_type == 'int'
					|| $field->field_type == 'FLOAT'
					|| $field->field_type =='INTEGER'
					|| $field->field_type =='BOOLEAN')
				array_push($numerical, $field->varname);

			if ($field->field_type == 'DROPDOWNLIST')
				array_push($safe, $field->varname);

			if ($field->field_type == 'VARCHAR' || $field->field_type == 'TEXT') {
				$field_rule = array($field->varname,
						'length',
						'max'=>$field->field_size,
						'min' => $field->field_size_min);

				if ($field->error_message)
					$field_rule['message'] = Yii::t('site', $field->error_message);

				array_push($rules,$field_rule);
			}

			if ($field->match) {
				$field_rule = array($field->varname,
						'match',
						'pattern' => $field->match);

				if ($field->error_message)
					$field_rule['message'] = Yii::t('site',  $field->error_message);

				array_push($rules,$field_rule);
			}

			if ($field->range) {
				// allow using commas and semicolons
				$range=explode(';',$field->range);
				if(count($range)===1)
					$range=explode(',',$field->range);
				$field_rule = array($field->varname,'in','range' => $range);

				if ($field->error_message)
					$field_rule['message'] = Yii::t('site',  $field->error_message);
				array_push($rules,$field_rule);
			}

			if ($field->other_validator) {
				$field_rule = array($field->varname,
						$field->other_validator);

				if ($field->error_message)
					$field_rule['message'] = Yii::t('site',  $field->error_message);
				array_push($rules, $field_rule);
			}

		}

		array_push($rules,
				array(implode(',',$required), 'required'));
		array_push($rules,
				array(implode(',',$numerical), 'numerical', 'integerOnly'=>true));
		array_push($rules,
				array(implode(',',$safe), 'safe'));

		$rules[] = array('allow_comments, show_friends', 'numerical');
		$rules[] = array('email', 'unique', 'on' => array('insert', 'registration'), 'message' => Yii::t('site', 'Данный email занят'));
		$rules[] = array('email', 'CEmailValidator', 'message' => Yii::t('site', 'Wrong email'));
		$rules[] = array('privacy', 'safe');

        $rules[] = array('email', 'required', 'on' => array('insert', 'registration'), 'message' => Yii::t('site', 'Email is required'));
        $rules[] = array('firstname', 'required', 'message' => Yii::t('site', 'First name is required'));
        $rules[] = array('lastname', 'required', 'message' => Yii::t('site', 'Last name is required'));

		return $rules;
	}

	public function relations()
	{
		$relations = array(
				'user' => array(self::BELONGS_TO, 'YumUser', 'user_id'),
				'comments' => array(self::HAS_MANY, 'YumProfileComment', 'profile_id'),
				);

		$fields = Yii::app()->db->cache(3600)->createCommand(
				"select * from ".YumProfileField::model()->tableName()." where field_type = 'DROPDOWNLIST'")->queryAll();

		foreach($fields as $field) {
			$relations[ucfirst($field['varname'])] = array(
					self::BELONGS_TO, ucfirst($field['varname']), $field['varname']);

		}

		return $relations;
	}

	// Retrieve a list of all users that have commented my profile
	// Do not show my own profile visit
	public function getProfileCommentators() {
		$commentators = array();
		foreach($this->comments as $comment)
			if($comment->user_id != Yii::app()->user->id)
				$commentators[$comment->user_id] = $comment->user;

		return $commentators;
	}

	public function getProfileFields() {
		$fields = array();

		if(self::$fields)
			foreach(self::$fields as $field) {
				$varname = $field->varname;
				$fields[$varname] = Yii::t('site', $varname);
			}
		return $fields;
	}

	public function name() {
		return sprintf('%s %s', $this->firstname, $this->lastname);
	}

	public function attributeLabels()
	{
		$labels = array(
                'firstname'      => Yii::t('site', 'Firstname'),
                'lastname'       => Yii::t('site', 'Lastname'),
				'id'             => Yii::t('site', 'Profile ID'),
				'email'          => Yii::t('site', 'Email'),
				'Name'           => Yii::t('site', 'Name'),
				'user_id'        => Yii::t('site', 'User ID'),
				'privacy'        => Yii::t('site', 'Privacy'),
				'show_friends'   => Yii::t('site', 'Show friends'),
				'allow_comments' => Yii::t('site', 'Allow profile comments'),
				);

		if(self::$fields)
			foreach (self::$fields as $field)
				$labels[$field->varname] = Yii::t('site', $field->title);

		return $labels;
	}

	/**
	 * Load profile fields.
	 * Overwrite this method to get another set of fields
	 * Makes use of cache so the amount of sql queries per request is reduced
	 * @since 0.6
	 * @return array of YumProfileFields or empty array
	 */
	public function loadProfileFields()
	{
		if(self::$fields===null)
		{
			self::$fields=YumProfileField::model()->cache(3600)->findAll();
			if(self::$fields==null)
				self::$fields=array();
		}
		return self::$fields;
	}
}
