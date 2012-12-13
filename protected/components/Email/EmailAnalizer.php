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
    public $inboxEmailTrashFolderId = null;
    
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
    
    /**
     * @param array of MailTemplates
     */
    public $mailTemplate = array();
    
    /**
     * @param array of MailTask
     */
    public $mailTasks = array();


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
                $this->outboxEmailFolderId = (int)$mailFolder->id;
            }
            if ('Корзина' === trim($mailFolder->name)) {
                $this->inboxEmailTrashFolderId = (int)$mailFolder->id;
            }
        }
        
        $subScreens = array_flip(LogHelper::getSubScreensArr());
        
        $this->mailMainWindowId   = (int)$subScreens[LogHelper::MAIL_MAIN];
        $this->mailPlanWindowId   = (int)$subScreens[LogHelper::MAIL_PLAN];
        $this->mailNewWindowId    = (int)$subScreens[LogHelper::MAIL_NEW];
        $this->mailPreviewindowId = (int)$subScreens[LogHelper::MAIL_PREVIEW];    
        
        
        // get mail templates
        foreach(MailTemplateModel::model()->findAll() as $mailTemplate) {
            $this->mailTemplate[$mailTemplate->code] = $mailTemplate; 
        }
        unset($mailTemplate); 
        
        
        // populate with right Mail_tasks
        foreach(MailTasksModel::model()->byWrongRight('R')->findAll() as $mailTask) {
            $this->mailTasks[$mailTask->code] = $mailTask;
        }
        unset($mailTask);
        
        /**
         * Get emails
         */
        foreach (MailBoxModel::model()->bySimulation($this->simId)->findAll() as $email) {
            $this->userEmails[$email->id] = new EmailData($email);
            
            if (isset($this->mailTemplate[$email->code])) {
                $this->userEmails[$email->id]->setTypeOfImportance(
                    $this->mailTemplate[$email->code]->type_of_importance
                );
                
                //var_dump($email->code, $this->userEmails[$email->id]->typeOfImportance);
            }
            
            if (isset($this->mailTasks[$email->code])) {
                $this->userEmails[$email->id]->setRightPlanedTaskId(
                    $this->mailTasks[$email->code]->id
                );
            }
        }
        unset($email);
        
        /**
         * Add readedAt, plannedAt, replyedAt
         */
        foreach (LogMail::model()->bySimId($this->simId)->findAll() as $logMailLine) {
            $mailId = $logMailLine->mail_id;
            
            // we can have not saved letter in log, so there is no mail_box letter for it
            if (isset($this->userEmails[$mailId])) {
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
                    $userEmail->getIsPlaned()) {
                    $this->userEmails[$mailId]->setPlanedAt($logMailLine->start_time);
                } 

                //var_dump($logMailLine->id);

                // add planned mail_task.id
                if (null !== $logMailLine->mail_task_id) {

                    $this->userEmails[$mailId]->setPlanedTaskId($logMailLine->mail_task_id);
                }
            }
        }
        unset($logMailLine);
        
        /**
         * Update (add) replied at
         */
        foreach ($this->userEmails as $mailId => $emailData) {
            if (null !== $emailData->getParentEmailId()) {
                $this->userEmails[$emailData->getParentEmailId()]->setAnsweredAt($emailData->email->sending_time);
                $this->userEmails[$emailData->getParentEmailId()]->answerEmailId = $emailData->email->id;
            }
        }
        
        /**
         * Separate emails
         */
        foreach ($this->userEmails as $mailId => $emailData) { 
            if ($this->isInbox($emailData->email) || $this->isInTrash($emailData->email)) {                
                $this->userInboxEmails[$mailId] = $emailData;
            } elseif ($this->isOutbox($emailData->email)) {
                //var_dump($mailId);
                $this->userOutboxEmails[$mailId] = $emailData;
            }
        }        
    }
    
    /**
     * 3322 - Add to plan right tasks
     * 3324 - Add to plan wrong tasks
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    public function check_3322_3324()
    {
        $possibleRightActions = 0;
        $doneRightActions = 0;
        $wrongActions = 0;
        
        // inbox + trashCan
        foreach ($this->userInboxEmails as $mailId => $emailData) {
            
            // need to be planed?
            if (true === $emailData->isNeedToBePlaned()) {
                $possibleRightActions++;

                // is user add to plan right mail_task
                if ($emailData->getPlanedTaskId() === $emailData->getRightPlanedTaskId()) {
                    $doneRightActions++;
                }
            } else {
                //var_dump($emailData->email->id);
                // -> no needs to add task to plan
                if (true === $emailData->getIsPlaned()) {
                    // but user has add it to plan - wrong action
                    $wrongActions++;
                }
            }
        } 
        
        $behave_3322 = CharactersPointsTitles::model()->byCode('3322')->positive()->find();
        $behave_3324 = CharactersPointsTitles::model()->byCode('3324')->negative()->find();
        
        $possibleRightActions = (0 === $possibleRightActions) ? 1 : $possibleRightActions;        
        
        return array(
            '3322' => array(
                'positive' => ($doneRightActions / $possibleRightActions) * $behave_3322->scale,
                'obj'      => $behave_3322,
            ),
            '3324' => array(
                'negative' => $wrongActions * $behave_3324->scale,
                'obj'      => $behave_3324,
            ),
        );
    }
    
    
    /**
     * 3325 - read spam
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    public function check_3325()
    {
        $wrongActions = 0;
        
        // inbox + trashCan
        foreach ($this->userInboxEmails as $emailData) {
            //var_dump($emailData->email->id);
            if (true === $emailData->getIsSpam() && true === $emailData->getIsReaded()) {
                
                $wrongActions++;
            }
        } 
        
        $behave_3325 = CharactersPointsTitles::model()->byCode('3325')->negative()->find();
        
        return array(
            'negative' => $wrongActions * $behave_3325->scale,
            'obj'      => $behave_3325,
        );
    }
    /**
     * 3323 - In 2 real minutes (16 game min) react on issues 
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    public function check_3323($delta = 24)
    {
        $possibleRightActions = 0;
        $doneRightActions = 0;
        
        // inbox + trashCan
        foreach ($this->userInboxEmails as $mailId => $emailData) {
            
            if (true === $emailData->isNeedToActInTwoMinutes()) {
                $possibleRightActions++;
                
                if ($emailData->isAnsweredByMinutes($delta)) {
                    $doneRightActions++;
                }
            }
        } 
        
        $behave_3323 = CharactersPointsTitles::model()->byCode('3323')->positive()->find();
         
        $possibleRightActions = (0 === $possibleRightActions) ? 1 : $possibleRightActions;        
        
        return array(
            'positive' => ($doneRightActions / $possibleRightActions) * $behave_3323->scale,
            'obj'      => $behave_3323,
        );
    }
    
    /**
     * @deprecated, was used just to test Analizer
     * 
     * Sample analize 'Is user add to plan big task emails, in 16 game minutes'
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    /*public function checkBigTasks($delta = 16)
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
    }*/
    
    /**
     * @deprecated, was used just to test Analizer
     * 
     * Sample analize 'Is user reply for small task emails, in 16 game minutes'
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    /*public function checkSmallTasks($delta = 16)
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
    }*/
    
    /**
     * @deprecated, was used just to test Analizer
     * 
     * Sample analize 'Is user read spam'
     * 
     * @return mixed array
     */
    /*public function checkSpam()
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
    }*/
    
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
    private function isInTrash($email)
    {
        return ($this->inboxEmailTrashFolderId === (int)$email->group_id);
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

