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
    public function __construct($email, $password, $isByCookie = false)
    {
        $this->username   = $email;
        $this->email      = $email;
        $this->password   = $password;
    }
    
    public function authenticate()
    {
        $record = Users::model()->findByAttributes(array('email'=>$this->email));
        
        if($record === null) {
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        } else if($record->password !== $record->encryptPassword($this->password)) {
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        } else {
            $this->_id = $record->id;
            // we use API, so this set cookie will never apply
            $this->setState('useremail', $record->email); 
            $this->errorCode = self::ERROR_NONE;
        }
        
        return !$this->errorCode;
    }
 
    public function getId()
    {
        return $this->_id;
    }    
}

