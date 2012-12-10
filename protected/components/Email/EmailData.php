<?php

/**
 *
 * @author slavka
 */
class EmailData 
{
    public $email = null;
    
    public $isMustBeAnsweredInTwoMinutes = false;
    
    public $firstOpenedAt = null;
    
    public $answeredAt = null;
    
    public $plannedAt = null;
    
    public $isSpam = false;
    
    /**
     * @param MailBox instance $email
     */
    public function __construct($email) {
        $this->email = $email;
    }
}

