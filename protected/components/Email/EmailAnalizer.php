<?php

/**
 *
 * @author slavka
 */
class EmailAnalizer 
{
    /**
     * @var array of EmailData, indexed of MySQL email.template_id
     */
    public $userEmails = array(); 
    
    /**
     * @var array of EmailData, indexed of MySQL email.template_id
     */
    public $userInboxEmails = array();
    
    /**
     * @var array of EmailData, indexed of MySQL email.template_id
     */
    public $userOutboxEmails = array();
    
    /**
     * @var integer
     */
    public $simId = null; 
    
    /**
     * @var integer
     */
    public $inboxEmailFolderId = null;
    
    /**
     * @var integer
     */
    public $outboxEmailFolderId = null;
    
    /**
     * @var integer
     */
    public $mailMainWindowId = null;
    
    /**
     * @var integer
     */
    public $mailPreviewindowId = null;
    
    /**
     * @var integer
     */
    public $mailNewWindowId = null;
    
    /**
     * @var integer
     */
    public $mailPlanWindowId = null;
    
    public function __construct($simId) 
    {
        $this->simId = $simId;
        
        /**
         * Get mail folder ids
         */
        foreach (MailFoldersModel::model()->findAll($this->simId) as $mailFolder) {
            if ('Входящие' === trim($mailFolder->name)) {
                $this->inboxEmailFolderId = (int)$mailFolder->id;
            }
            if ('Исходящие' === trim($mailFolder->name)) {
                $this->inboxEmailFolderId = (int)$mailFolder->id;
            }
        }
        
        $subScreens = array_flip(LogHelper::getSubScreensArr());
        
        $this->mailMainWindowId   = (int)$subScreens[LogHelper::MAIL_MAIN];
        $this->mailPlanWindowId   = (int)$subScreens[LogHelper::MAIL_PLAN];
        $this->mailNewWindowId    = (int)$subScreens[LogHelper::MAIL_NEW];
        $this->mailPreviewindowId = (int)$subScreens[LogHelper::MAIL_PREVIEW];        
        
        /**
         * Get emails
         */
        foreach (MailBoxModel::model()->bySimulation($this->simId)->findAll() as $email) {
            $this->userEmails[$email->id] = new EmailData($email);
        }
        unset($email);
        
        /**
         * Add readedAt, plannedAt, replyedAt
         */
        foreach (LogMail::model()->bySimId($this->simId)->findAll() as $logMailLine) {
            $mailId = $logMailLine->mail_id;
            $userEmail = $this->userEmails[$mailId];
            
            // check when letter was readed at first
            if (null === $userEmail->getFirstOpenedAt() || 
                $logMailLine->start_time < $userEmail->getFirstOpenedAt()) {
                $this->userEmails[$mailId]->setFirstOpenedAt($logMailLine->start_time);
            }
            
            // check when letter was Planed, at first
            // open planer and close it?
            if ((null === $userEmail->getPlanedAt() || $logMailLine->start_time < $userEmail->getPlanedAt()) &&
                $this->mailPlanWindowId === (int)$logMailLine->window &&
                $mailId->getIsPlaned()) {
                $this->userEmails[$mailId]->setPlanedAt($logMailLine->start_time);
            }            
        }
        unset($logMailLine);
        
        /**
         * Update (add) replied at
         */
        foreach ($this->userEmails as $mailId => $emailData) {
            if (null !== $emailData->getParentEmailId()) {
                $this->userEmails[$emailData->getParentEmailId()]->setAnsweredAt($emailData->email->sending_time);
            }
        }
        
        /**
         * Update (add) isSpam
         */
        // ...
        
        /**
         * Update (add) isTaskToPlan
         */
        // ...
        
        /**
         * Update (add) isNeedAnyReaction
         */
        // ...   
        
        /**
         * Update (add) isNeedReply
         */
        // ... 
        
        /**
         * Separate emails
         */
        foreach ($this->userEmails as $mailId => $emailData) {
            
            if ($this->isInbox($emailData->email)) {
                
                $this->userInboxEmails[$mailId] = $emailData;
            } elseif ($this->isOutbox($emailData->email)) {
                var_dump($mailId);
                $this->userOutboxEmails[$mailId] = $emailData;
            }
        }        
    }
    
    /**
     * Sample analize 'Is user add to plan big task emails, in 16 game minutes'
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    public function checkBigTasks($delta = 16)
    {
        $rightActions = 0;
        $wrongActions = 0;
        foreach ($this->userInboxEmails as $mailId => $emailData) {
            if ($emailData->isBigTask) {
                if ($emailData->isPlanedByMinutes($delta)) {
                    $rightActions++;
                } else {
                    $wrongActions++;
                }
            }
        } 
        
        return array(
            'rightActions' => $rightActions,
            'wrongActions' => $wrongActions,
        );
    }
    
    /**
     * Sample analize 'Is user reply for small task emails, in 16 game minutes'
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    public function checkSmallTasks($delta = 16)
    {
        $rightActions = 0;
        $wrongActions = 0;
        foreach ($this->userInboxEmails as $mailId => $emailData) {
            if ($emailData->isSmallTask) {
                if ($emailData->isAnsweredByMinutes($delta)) {
                    $rightActions++;
                } else {
                    $wrongActions++;
                }
            }
        } 
        
        return array(
            'rightActions' => $rightActions,
            'wrongActions' => $wrongActions,
        );
    }
    
    /**
     * Sample analize 'Is user read spam'
     * 
     * @return mixed array
     */
    public function checkSpam()
    {
        $rightActions = 0;
        $wrongActions = 0;
        foreach ($this->userInboxEmails as $mailId => $emailData) {
            if ($emailData->getIsSpam()) {
                if ($emailData->getIsReaded()) {
                    $wrongActions++;
                } else {
                    $rightActions++;
                }
            }
        } 
        
        return array(
            'rightActions' => $rightActions,
            'wrongActions' => $wrongActions,
        );
    }
    
    // --- tools: ------------------------------------------------------------------------------------------------------
    
        /**
     * @param MailBoxModel $email
     * @return boolean
     */
    private function isInbox($email)
    {
        return ($this->inboxEmailFolderId === (int)$email->group_id);
    }
    
    /**
     * @param MailBoxModel $email
     * @return boolean
     */
    private function isOutbox($email)
    {
        return ($this->outboxEmailFolderId === (int)$email->group_id);
    }
}

