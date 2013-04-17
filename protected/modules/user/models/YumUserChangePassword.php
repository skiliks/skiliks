<?php
/**
 * UserChangePassword class.
 * UserChangePassword is the data structure for keeping
 * user change password form data. It is used by the 'changepassword' action
 * of 'UserController'.
 */

class YumUserChangePassword extends YumFormModel 
{
//	private $_errors;
	public $password;
	public $verifyPassword;
	public $currentPassword;

	public function addError($attribute, $error) {
		parent::addError($attribute, Yum::t($error));
	}

	public function rules() {
		$passwordRequirements = Yum::module()->passwordRequirements;

        $rules[] = array('currentPassword', 'safe');
        $rules[] = array('currentPassword', 'required', 'on' => 'user_request', 'message' => Yii::t('site', 'Password is required'));
        $rules[] = array('password', 'required', 'message' => Yii::t('site', 'Password is required'));
        $rules[] = array('verifyPassword', 'required', 'message' => Yii::t('site', 'Repeat password'));
        $rules[] = array('password', 'compare', 'compareAttribute' =>'verifyPassword', 'message' => Yii::t('site', 'Passwords do not match'));

		$passwordrule = array_merge(array('password', 'YumPasswordValidator'), $passwordRequirements);
		$rules[] = $passwordrule;

		return $rules;
	}

	public function attributeLabels() {
		return array(
			'password'=>Yum::t('Введите новый пароль'),
			'verifyPassword'=>Yum::t('Повторите новый пароль'),
			'currentPassword'=>Yum::t('Ваш текущий пароль'),
		);
	}

}
