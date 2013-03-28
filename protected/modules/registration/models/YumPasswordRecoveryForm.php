<?php

/**
 * PasswordRecoveryForm class.
 * PasswordRecoveryForm is the data structure for keeping the
 * password recovery form data. It is used by the 'recovery' action 
 * of the Registration Module
 */
class YumPasswordRecoveryForm extends YumFormModel {

	public $email;

	// $user will be poupulated with the user instance, if found
	public $user;
	
	public function rules()
	{
		$rules = array(
				// username and password are required
				array('email', 'required'),
				array('email', 'checkexists'),
				array('email', 'email'),
				);

		return $rules;
	}

	public function attributeLabels()
	{
		return array(
			'email'=>Yum::t('Email'),
		);
	}
	
	public function checkexists($attribute, $params) {
		$user = null;

		// we only want to authenticate when there are no input errors so far
		if(!$this->hasErrors()) {
			if (strpos($this->email,"@")) {
				$profile = YumProfile::model()->findByAttributes(array(
							'email'=>$this->email));
				$this->user = $profile 
					&& $profile->user 
					&& $profile->user instanceof YumUser ? $profile->user : null;
			}
		}
	}
}
