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
    
    /**behave_3313
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
    public $rightMailTasks = array();
    
    /**
     * @param array of MailTask
     */
    public $wrongMailTasks = array();
    
    /**
     * @param array of MailTask
     */
    public $neutralMailTasks = array();
    
    /**
     * @param array of MailPoint
     */
    public $mailPoints = array();
    
    /**
     * @param array of point, indexed by id
     */
    public $points = array();
    
    public $template_reply_all = array();
    
    public $full_coincidence_reply_all = array();
    
    public $reply_all = array();

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
            if($mailTemplate->type_of_importance === "reply_all") {
                $this->template_reply_all[] = $mailTemplate->code;
            }
        }
        unset($mailTemplate);        
        
        // populate with right Mail_tasks
        foreach(MailTasksModel::model()->byWrongRight('R')->findAll() as $mailTask) {
            $this->rightMailTasks[$mailTask->code] = $mailTask;
        }
        unset($mailTask);
        
        // populate with wrong Mail_tasks
        foreach(MailTasksModel::model()->byWrongRight('W')->findAll() as $mailTask) {
            $this->wrongMailTasks[$mailTask->id] = $mailTask;
        }
        unset($mailTask);
        
        // populate with neutral Mail_tasks
        foreach(MailTasksModel::model()->byWrongRight('N')->findAll() as $mailTask) {
            $this->neutralMailTasks[$mailTask->id] = $mailTask;
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
            }
            
            if (isset($this->rightMailTasks[$email->code])) {
                $this->userEmails[$email->id]->setRightPlanedTaskId(
                    $this->rightMailTasks[$email->code]->id
                );
            }
        }
        unset($email);
        
        /**
         * Add readedAt, plannedAt, replyedAt
         */
        $temp_log_mail = LogMail::model()->bySimId($this->simId)->findAll();
        foreach ($temp_log_mail as $logMailLine) {
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
                // sending time for sending message saved in seconds from 00:00:00 game day 1
                $this->userEmails[$emailData->getParentEmailId()]->setAnsweredAt($emailData->email->sent_at);
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
                $this->userOutboxEmails[$mailId] = $emailData;
            }

        }  
        
        /**
         * Get character points
         */        
        foreach (CharactersPointsTitles::model()->findAll() as $point) {
            $this->points[$point->id] = $point;
        }
        unset($point);
        
        /**
         * Get mail points
         */        
        foreach (MailPointsModel::model()->findAll() as $point) {
            $this->mailPoints[$point->id] = $point;
        }
        unset($point);
        $temp = array();
        foreach ($temp_log_mail as $mail) {
                $temp[] = array($mail->full_coincidence, $mail->mail_id);
                if(isset($this->userOutboxEmails[$mail->mail_id]) 
                        AND $this->userOutboxEmails[$mail->mail_id]->email->letter_type === 'replyAll' 
                        AND $this->userOutboxEmails[$mail->mail_id]->email->group_id == 3) {
                        if($mail->full_coincidence === '-' OR $mail->full_coincidence === null OR $mail->full_coincidence === ''){
                            $this->reply_all[] = $this->userOutboxEmails[$mail->mail_id]->email->code;
                        }else{
                            $this->full_coincidence_reply_all[] = $mail->full_coincidence;
                        }
                       
                }
        }
    }
    
    /** ----------------------------------------------------- **/
    
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
                
                if ($this->isMailTaskHasRightAction($emailData->email->template_id)) {
                    $possibleRightActions++;
                }
                
                if (true === $emailData->getIsPlaned()) {
                    // is user add to plan right mail_task ?
                    if ($emailData->getPlanedTaskId() === $emailData->getRightPlanedTaskId()) {

                        $doneRightActions++;
                    // is user add to plan wrong mail_task ?
                    } elseif (true === $this->isWrongMailTaskAction($emailData->getPlanedTaskId())) {
                        $wrongActions++;
                    }
                    // else are Neutral tasks
                }
            } else {                
                // -> no needs to add task to plan
                if (true === $emailData->getIsPlaned() && false === $this->isNeutralMailTaskAction($emailData->getPlanedTaskId())) {
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
     * 3313 - read all emails, exept spam
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    public function check_3313($limit = 0.9)
    {
        $possibleRightActions = 0;
        $rightActions = 0;
        
        // inbox + trashCan
        foreach ($this->userInboxEmails as $emailData) {
            if (false === $emailData->getIsSpam() ) {
                $possibleRightActions++;
                
                if (true === $emailData->getIsReaded()) {
                    $rightActions++;
                }
            }
        } 
        
        $behave_3313 = CharactersPointsTitles::model()->byCode('3313')->positive()->find();
        
        // grand score for user, if he read more or equal to $limit of not-spam emails only
        $mark = 0;
        if ($limit <= $rightActions/$possibleRightActions) {
            $mark = 1;
        }
        
        return array(
            'positive' => $mark * $behave_3313->scale,
            'obj'      => $behave_3313,
        );
    }
    
    /**
     * 3325 - read spam
     * 
     * @param integer $delta
     * 
     * @return mixed array
     */
    public function check_3333()
    {
        $wrongActions = 0;
        
        // inbox + trashCan
        if(count($this->reply_all)!= 0){
            $wrongActions++;
        }
        foreach ($this->full_coincidence_reply_all as $coincidence) {
            if (!in_array($coincidence, $this->template_reply_all)) {
                $wrongActions++;
            }
        } 
        
        $behave_3333 = CharactersPointsTitles::model()->byCode('3333')->positive()->find();
        
        return array(
            'positive' => ($wrongActions == 0)?$behave_3333->scale:0,
            'obj'      => $behave_3333,
        );
    }
    
    public function getExceptionPointCodes()
    {
        return array(
            '3313', '3322', '3323', '3324', '3325'
        );
    }

    public function standardCheck()
    {
        $mailBehaviours = array();
        
        foreach ($this->mailPoints as $mailPoint) {
            $code = $this->points[$mailPoint->point_id]->code;
            // check only existed mailPoints
            if (false === in_array($code, $this->getExceptionPointCodes())) {
                $mailBehaviours[$mailPoint->point_id] = array(
                    'total' => 0,
                    'score' => 0,
                );
            }
        }
        
        // use all emails in simulation
        foreach ($this->userEmails as $emailData) {
            // points must be calculated for readed or sended emails only
              
            if ($this->isOutbox($emailData->email) || 
                $this->isInbox($emailData->email) || 
                $this->isInTrash($emailData->email)) {
                
                // go throw ailPoints
                foreach ($this->mailPoints as $mailPoint) {
                    // exept special scored points
                    $code = $this->points[$mailPoint->point_id]->code;
                    if (false === in_array($code, $this->getExceptionPointCodes())) {
                         
                        if ($mailPoint->mail_id == $emailData->email->template_id) {
                            $mailBehaviours[$mailPoint->point_id]['total']++;
                            $mailBehaviours[$mailPoint->point_id]['score'] = $mailPoint->add_value;
                        }
                    }
                }
            }
        }
        
        $behaves = array();
        foreach ($this->points as $behave) {
            $behaves[$behave->id] = $behave;
        }
        unset($behave);
        
        foreach ($mailBehaviours as $pointId => $mBehave) {
            if (0 == $mBehave['total']) { 
                $mBehave['total'] = 1; // prevent devision by zero. If total = 0, than score = 0 too. So value wiil be right.         
            }
            
            $k = $behaves[$pointId]->scale;
            if (2 == $behaves[$pointId]->type_scale) {
                $mailBehaviours[$pointId]['value'] = -$k;
            } else {
                $mailBehaviours[$pointId]['value'] = ($mBehave['score']/$mBehave['total'])*$k;
            }
            $mailBehaviours[$pointId]['obj'] = $behaves[$pointId];
        }
        
        return $mailBehaviours;
        
        // return point array
    }

    // --- tools: ------------------------------------------------------------------------------------------------------
    
    /**
     * @param integer $mailTaskId
     * @return boolean
     */
    private function isMailTaskHasRightAction($mailTemplateId)
    {
        $taskWays = MailTasksModel::model()->byMailId($mailTemplateId)->byWrongRight('R')->findAll();
       
        return (0 < count($taskWays) && null !== $taskWays);
    }
    
    /**
     * @param integer $id, MailTask.id
     * @return boolean
     */
    private function isWrongMailTaskAction($id)
    {
        return isset($this->wrongMailTasks[$id]);
    }
    
    /**
     * @param integer $id, MailTask.id
     * @return boolean
     */    
    private function isNeutralMailTaskAction($id)
    {
        return isset($this->neutralMailTasks[$id]);
    }
    
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

