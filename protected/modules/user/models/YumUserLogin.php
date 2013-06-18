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
    public $user = null;
    public $profile = null;
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
            array('username', 'required', 'on' => 'login_admin', 'message' => Yii::t('site', 'Login (email) is required')),
            array('username', 'CEmailValidator', 'message' => Yii::t('site', 'Wrong email')),
            array('password', 'required', 'on' => 'login_admin', 'message' => Yii::t('site', 'Password is required')),
            array('username' , 'loginByUsernameAdmin', 'on' => 'login_admin', 'message' => Yii::t('site', 'Имя пользователя или неверный пароль')),
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

        /* @var $this->user YumUser */
        /* @var $this->profile YumProfile */
        $this->profile = YumProfile::model()->findByAttributes(['email'=>$this->username]);
        if(null !== $this->profile) {
            $this->user = YumUser::model()->findByPK($this->profile->user_id);
            if(null !== $this->user) {
                if(YumEncrypt::encrypt($this->password, $this->user->salt) === $this->user->password){
                    return true;
                }else{
                    $this->addError('password', Yum::t('Неверный пароль'));
                    return false;
                }
            }else{
                $this->addError('username', Yum::t('Неверный логин'));
                return false;
            }
        }else{
            $this->addError('username', Yum::t('Неверный логин'));
            return false;
        }

    }

    public function loginByUsernameAdmin() {
        if($this->loginByUsername()){
            /* @var $this->user YumUser */
            /* @var $this->profile YumProfile */
                if(null !== $this->user) {
                    if($this->user->isAdmin()){
                        return true;
                    }else{
                        $this->addError('password', Yum::t('Неверный пароль'));
                        return false;
                    }
            }
        }
    }

 }
