<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'YumUserController'.
 */
class YumUserLogin extends YumFormModel {
	public $username;
	public $password;
	public $rememberMe;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		if(!isset($this->scenario))
			$this->scenario = 'login';

		$rules = array(
			array('username', 'required', 'on' => 'login', 'message' => Yii::t('site', 'Login (email) is required')),
            array('username', 'CEmailValidator', 'message' => Yii::t('site', 'Wrong email')),
			array('password', 'required', 'on' => 'login', 'message' => Yii::t('site', 'Password is required')),
            array('username' , 'loginByUsername', 'on' => 'login', 'message' => Yii::t('site', 'Имя пользователя или неверный пароль')),
			array('username', 'required', 'on' => 'openid'),
			array('rememberMe', 'boolean'),
		);

		return $rules;
	}

	public function attributeLabels() {
		return array(
			'username'=>Yum::t('Email'),
			'password'=>Yum::t("Пароль"),
			'rememberMe'=>Yum::t("Remember me next time"),
		);
	}

    public function loginByUsername() {

        /* @var $user YumUser */
        /* @var $profile YumProfile */
        $profile = YumProfile::model()->findByAttributes(['email'=>$this->username]);
        if(null !== $profile) {
            $user = YumUser::model()->findByPK($profile->user_id);
            if(null !== $user) {
                if(YumEncrypt::encrypt($this->password, $user->salt) === $user->password){
                    return true;
                }else{
                    $this->addError('password', Yum::t('Неверный пароль'));
                }
            }else{
                $this->addError('username', Yum::t('Неверный логин'));
            }
        }else{
            $this->addError('username', Yum::t('Неверный логин'));
        }

    }

}
