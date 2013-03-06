<?php

/**
 *
 * @author slavka
 */
class BackendUserIdentity extends CUserIdentity
{
    private $_id;
    
    /* @var string */
    public $email;
    
    /* 
     * is systen tries to authentificate user by email saved in cookie
     * @var bolean 
     */
    public $isByCookie = false;
    
    /**
     * Constructor.
     * @param string $email email
     * @param string $password password
     */
    public function __construct($username, $password, $isByCookie = false)
    {
        $this->username   = $username;
        $this->email      = '';
        $this->password   = $password;
    }
    
    public function authenticate()
    {
        $user = YumUser::model()->findByAttributes(array('username'=>$this->username));
        
        if($user === null) {
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        } else if($user->password !== $user->encryptPassword($this->password)) {
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        } else {
            $this->_id = $$user->id;
            // we use API, so this set cookie will never apply
            $this->setState('useremail', $user->profile->email);
            $this->errorCode = self::ERROR_NONE;
        }
        
        return !$this->errorCode;
    }
 
    public function getId()
    {
        return $this->_id;
    }    
}

