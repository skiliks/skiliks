<?php

/**
 *
 * @author slavka
 */
class EmailAnalizer 
{
    /**
     * @var array of EmailData, indexed of MySQL email id
     */
    public $userEmails = array(); 
    
    public function construct($simId) 
    {
        foreach (MailBoxModel::model()->bySimulation($simId)->findAll() as $email) {
            $userEmails[$email->id] = new EmailData($email);
        }
    }
}

