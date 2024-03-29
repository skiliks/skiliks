<?php

/**
 * Class YumProfile
 *
 * @property string $email
 * @property string $user_id
 * @property string $lastname
 * @property string $firstname
 * @property YumUser $user
 */

class YumProfile extends YumActiveRecord
{
	const PRIVACY_PRIVATE = 'private';
	const PRIVACY_PUBLIC  = 'public';

    /**
     * Типы итоговой оценоки:
     */
    const PERCENTILE = 'percentil';
    const STANDARD   = 'standard';

	/**
	 * @var array of YumProfileFields
	 */
	static $fields = null;

    public function switchAssessmentResultsRenderType() {
        if($this->assessment_results_render_type == self::PERCENTILE) {
            $this->assessment_results_render_type = self::STANDARD;
        } else {
            $this->assessment_results_render_type = self::PERCENTILE;
        }
    }

    // --------------------------------------------------------------------------------------------------------

    public function updateFirstNameFromEmail()
    {
        if (0 < strpos($this->email, '@')) {
            $this->firstname = substr(strtolower($this->email), 0, strpos(strtolower($this->email), '@'));
        } else {
            $this->firstname = strtolower($this->email);
        }
    }

    public function getEmailAlreadyExistMessage()
    {

        if(null === $this->id){
            $profile = $this->findByAttributes(['email'=>strtolower($this->email)]);
            if(null === $profile){
                throw new Exception("Profile by email {$this->email} not found!");
            }else{
                $id = $profile->id;
            }

        }else{
            $id = $this->id;
        }
        return Yii::t('site',  'Email already exists, but not activated.')
            . CHtml::link(
                Yii::t('site','Send activation again'),
                '/activation/resend/' . $id,
                ['class' => 'color-146672']
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

        $rules[] = array('email', 'isAccountBanned');
        $rules[] = array('email', 'emailIsNotActiveValidation', 'on' => array('insert', 'registration', 'registration_corporate', 'update', 'update_corporate'));
        $rules[] = array('email', 'emailIsUsedForCorporateAccount', 'on' => array('insert', 'registration', 'registration_corporate', 'update'));
        $rules[] = array('allow_comments, show_friends', 'numerical');
        $rules[] = array('email', 'unique', 'on' => array('insert', 'registration', 'registration_corporate'), 'message' => Yii::t('site', 'Данный email занят'));
		$rules[] = array('email', 'CEmailValidator', 'message' => Yii::t('site', 'Wrong email'));
        $rules[] = array('privacy', 'safe');

        $rules[] = array('email', 'required', 'on' => array('insert', 'registration', 'registration_corporate', 'update', 'update_corporate'), 'message' => Yii::t('site', 'Email is required'));
        $rules[] = array('email' , 'isCorporateEmail', 'on' => array('insert', 'registration_corporate', 'update_corporate'));
        $rules[] = array('firstname', 'required', 'message' => Yii::t('site', 'First name is required'));
        $rules[] = array('lastname', 'required', 'message' => Yii::t('site', 'Last name is required'));

        $rules[] = array('firstname', 'length', 'max' => 50);
        $rules[] = array('lastname',  'length', 'max' => 50);
        $rules[] = array('email',  'length', 'max' => 50);

		return $rules;
	}

    public function emailIsNotActiveValidation($attribute) {
        $existProfile = YumProfile::model()->findByAttributes([
            'email' => strtolower($this->email)
        ]);
        if(null !== $this->getError('is_baned')){
            return;
        }
        if ($existProfile !== NULL && !$existProfile->user->isActive()) {
                $error = Yii::t('site',  'Email already exists, but not activated.')
                    . CHtml::link(
                        Yii::t('site','Send activation again'),
                        '/activation/resend/' . $existProfile->id
                    );
                $this->addError('not_activated', $error);
        }
    }

    public function isAccountBanned($attribute) {

        $existProfile = YumProfile::model()->findByAttributes([
            'email' => strtolower($this->email)
        ]);

        if($existProfile !== NULL && $existProfile->user->isBanned()) {
            $error = $this->getAccountBannedErrorMessage();
            $this->addError("is_baned", $error);
        }
    }

    public function getAccountBannedErrorMessage() {
        return $error = Yii::t('site',  sprintf('Аккаунт %s заблокирован', $this->email));
    }



    public function emailIsNotActiveValidationStatic($email) {
        $existProfile = YumProfile::model()->findByAttributes([
            'email' => strtolower($email)
        ]);

        if ($existProfile !== NULL && !$existProfile->user->isActive()) {
            return Yii::t('site',  'Email already exists, but not activated.')
                . CHtml::link(
                    Yii::t('site','Send activation again'),
                    '/activation/resend/' . $existProfile->id
                );
        }
        return false;
    }

    public function isAccountBannedStatic($email) {

        $existProfile = YumProfile::model()->findByAttributes([
            'email' => strtolower($email)
        ]);

        if($existProfile !== NULL && $existProfile->user->isBanned()) {
            return $this->getAccountBannedErrorMessage();
        }

        return false;
    }

    public function emailIsUsedForCorporateAccount($attribute) {

        $existAccount = $this->findByAttributes([
            'email' => strtolower($this->email)
        ]);

        if ($existAccount !== NULL) {
            $error = Yii::t('site',  'Email is already in use.');
            $this->addError($attribute, $error);
        }
        return true;
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

    /**
     * @param $attribute, attribute name
     */
    public function isCorporateEmail($attribute)
    {
        // для тестировщиков, мы вообще не проверяем емейл на корпоративность
        if (isset(Yii::app()->request->cookies['anshydjcyfhbxnfybjcbsgcusb27djxhds9dshbc7ubwbcd7034n9'])) {
            return;
        }

        if(false == UserService::isCorporateEmail($this->$attribute)) {
            $this->addError($attribute, Yii::t('site', 'Type your corporate e-mail'));
        }
    }

    public function isNotPersonalEmail($attribute)
    {
        $userPersonal = YumProfile::model()->findByAttributes(["email" => $this->$attribute]);
        if($userPersonal !== null && $userPersonal->user_id !== $this->user_id) {
            $this->addError($attribute, Yii::t('site', 'Email is already taken'));
        }
    }
}
