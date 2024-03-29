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
				array('email', 'required', 'message' => Yii::t('site', 'Email is required')),
				array('email', 'checkexists', 'message' => Yii::t('site', 'User with this email does not exist')),

				array('email', 'email', 'message' => Yii::t('site', 'Wrong email')),
                array('email', 'isBanned'),
				);

		return $rules;
	}

	public function attributeLabels()
	{
		return array(
			'email'=>Yum::t('Email'),
		);
	}

    /**
     * @param string $attribute, attribute name
     * @param array $params
     */
	public function checkexists($attribute, $params) {
		$user = null;

		// we only want to authenticate when there are no input errors so far
		if(!$this->hasErrors()) {
			if (strpos($this->email,"@")) {
				$profile = YumProfile::model()->findByAttributes([
                    'email' => strtolower($this->email)
                ]);

                if ($profile && $profile->user) {
                    $this->user = $profile->user;
                } else {
                    $this->addError('email', Yii::t('site', 'User with this email does not exist'));
                }
			}
		}
	}

    /**
     * @param string $attribute, attribute name
     * @param array $params
     */
    public function isBanned($attribute, $params) {
        if(!$this->hasErrors()) {
            if($this->user !== null && YumUser::STATUS_ACTIVE != $this->user->status) {
                $this->addError('email', Yii::t('site', 'Ваш аккаунт заблокирован'));
            }

            if($this->user !== null && $this->user->is_password_bruteforce_detected) {
                $this->addError('bruteforce', Yii::t('site', 'Ваш аккаунт заблокирован'));
            }
        }
    }
}
