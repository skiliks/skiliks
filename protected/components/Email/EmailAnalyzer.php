<?php

/**
 *
 * @author slavka
 * @property Simulation $simulation
 */
class EmailAnalyzer
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


    public function __construct(Simulation $simulation)
    {
        $this->simId = $simulation->id;
        $this->simulation = $simulation;

        /**
         * Get mail folder ids
         */
        foreach (MailFolder::model()->findAll($this->simId) as $mailFolder) {
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
        foreach($this->simulation->game_type->getMailTemplates([]) as $mailTemplate) {
            $this->mailTemplate[$mailTemplate->code] = $mailTemplate;
            if($mailTemplate->type_of_importance === "reply_all") {
                $this->template_reply_all[] = $mailTemplate->code;
            }
        }
        unset($mailTemplate);        
        
        // populate with right Mail_tasks
        foreach($simulation->game_type->getMailTasks(['wr' =>'R']) as $mailTask) {
            $this->rightMailTasks[$mailTask->code] = $mailTask;
        }
        unset($mailTask);
        
        // populate with wrong Mail_tasks
        foreach($simulation->game_type->getMailTasks(['wr' =>'W']) as $mailTask) {
            $this->wrongMailTasks[$mailTask->id] = $mailTask;
        }
        unset($mailTask);
        
        // populate with neutral Mail_tasks
        foreach($simulation->game_type->getMailTasks(['wr' =>'N']) as $mailTask) {
            $this->neutralMailTasks[$mailTask->id] = $mailTask;
        }
        unset($mailTask);
        
        /**
         * Get emails
         */
        foreach (MailBox::model()->bySimulation($this->simId)->findAll() as $email) {
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
         * Add readedAt, plannedAt, repliedAt
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
            if (null !== $emailData->getParentEmailId() && 0 != $emailData->getParentEmailId()) {
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
        foreach ( $this->simulation->game_type->getHeroBehavours([]) as $point) {
            $this->points[$point->id] = $point;
        }
        unset($point);
        
        /**
         * Get mail points
         */        
        foreach ($this->simulation->game_type->getMailPoints([]) as $point) {
            $this->mailPoints[$point->id] = $point;
        }
        unset($point);
        $temp = array();
        foreach ($temp_log_mail as $mail) {
            $temp[] = array($mail->full_coincidence, $mail->mail_id);
            if(isset($this->userOutboxEmails[$mail->mail_id])
                AND $this->userOutboxEmails[$mail->mail_id]->email->letter_type === MailBox::TYPE_REPLY_ALL
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

        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($this->simId);
        $behave_3322 = $simulation->game_type->getHeroBehaviour(['code' => '3322', 'type_scale' => 1]);
        $behave_3324 = $simulation->game_type->getHeroBehaviour(['code' => '3324', 'type_scale' => 2]);
        
        $possibleRightActions = (0 === $possibleRightActions) ? 1 : $possibleRightActions;

        return array(
            '3322' => array(
                'positive' => $behave_3322 ? ($doneRightActions / $possibleRightActions) * $behave_3322->scale : 0,
                'obj'      => $behave_3322,
            ),
            '3324' => array(
                'negative' => $behave_3324 ? $wrongActions * $behave_3324->scale : 0,
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
        
        $behave_3325 = $this->simulation->game_type->getHeroBehaviour(['code' => '3325', 'type_scale' => 2]);
        
        return array(
            'negative' => $behave_3325 ? $wrongActions * $behave_3325->scale : 0,
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
    public function check_3323() //24*60
    {
        $delta = 2 * (int)Yii::app()->params['public']['skiliksSpeedFactor'] * 60;
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
        
        $behave_3323 = $this->simulation->game_type->getHeroBehaviour(['code' => '3323', 'type_scale' => 1]);
         
        $possibleRightActions = (0 === $possibleRightActions) ? 1 : $possibleRightActions;        
        
        return array(
            'positive' => $behave_3323 ? ($doneRightActions / $possibleRightActions) * $behave_3323->scale : 0,
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
        
        $behave_3313 = $this->simulation->game_type->getHeroBehaviour(['code'=> 3313, 'type_scale'=>1]);
        
        // grand score for user, if he read more or equal to $limit of not-spam emails only
        $mark = 0;
        if ($possibleRightActions === 0) {
            $mark = 0;
        } else if ($limit <= $rightActions/$possibleRightActions) {
            $mark = 1;
        }
        
        return array(
            'positive' => $behave_3313 ? $mark * $behave_3313->scale : 0,
            'obj'      => $behave_3313,
        );
    }
    
    /**
     * 3333 -
     *
     * @return mixed array
     */
    public function check_3333()
    {
        $wrongActions = 0;
        
        // outbox not MS
        if (count($this->reply_all) != 0) {
            $wrongActions++;
        }

        foreach ($this->full_coincidence_reply_all as $coincidence) {
            if (!in_array($coincidence, $this->template_reply_all)) {
                $wrongActions++;
            }
        } 
        
        $behave_3333 = $this->simulation->game_type->getHeroBehaviour(['code' => '3333', 'type_scale' => 1]);
        
        return array(
            'positive' => ($behave_3333 && $wrongActions == 0) ? $behave_3333->scale : 0,
            'obj'      => $behave_3333,
        );
    }

    /**
     * 3325 - read spam
     *
     * @param integer $delta
     *
     * @return mixed array
     */
    public function check_3326()
    {
        $configs = Yii::app()->params['analizer']['emails']['3326'];

        $limitToGetPoints  = $configs['limitToGetPoints'];
        $limitToGet1points = $configs['limitToGet1points'];
        $limitToGet2points = $configs['limitToGet2points'];

        $criteria = new CDbCriteria();
        $criteria->compare('wr', 'R');
        $criteria->addCondition('letter_number like "MS%"');
        $rightMsNumber = count($this->simulation->game_type->getCommunicationThemes($criteria));
        $behave_3326 = $this->simulation->game_type->getHeroBehaviour(['code' => '3326', 'type_scale' => 1]);

        // gather statistic  {
        $userRightEmailsArray = []; // email with same MSxx must be counted once only
        $userWrongEmails = 0;
        $userTotalEmails = count($this->userOutboxEmails);

        foreach ($this->userOutboxEmails as $emailData) {
            // @todo: remove trick
            // ignore MSY letters
            if (strstr($emailData->email->code, 'MSY')) {
                continue;
            }

            if ('R' == $emailData->email->subject_obj->wr) {
                $userRightEmailsArray[$emailData->email->code] = 'something';
            }
            if ('W' == $emailData->email->subject_obj->wr) {
                $userWrongEmails++;
            }

            $userTotalEmails++;
        }

        $userRightEmails = count($userRightEmailsArray);
        // gather statistic }

        // 0 points if user had send too less emails (no matter W or R, or N)
        if ($userRightEmails/$rightMsNumber < (float)$limitToGetPoints) {
            return array(
                'positive' => 0,
                'obj'      => $behave_3326,
            );
        }

        // 2 points
        if ($userWrongEmails/$userRightEmails < $limitToGet2points) {
            return array(
                'positive' => $behave_3326->scale,
                'obj'      => $behave_3326,
            );
        }

        // 1 point
        if ($userWrongEmails/$userRightEmails < $limitToGet1points) {
            return array(
                'positive' => $behave_3326->scale*0.5,
                'obj'      => $behave_3326,
            );
        }

        // user write too much not right emails
        return array(
            'positive' => 0,
            'obj'      => $behave_3326,
        );
    }

    public function check_3311()
    {
        $behave_3311 = $this->simulation->game_type->getHeroBehaviour(['code' => '3311']);

        if (null === $behave_3311) {
            return [
                'case' => -2,
            ];
        }

        $countInboxRead = MailBox::model()->countByAttributes([
            'sim_id'   => $this->simulation->id,
            'group_id' => MailBox::FOLDER_INBOX_ID,
            'readed'   => 1,
        ]);

        $countTrashRead = MailBox::model()->countByAttributes([
            'sim_id'   => $this->simulation->id,
            'group_id' => MailBox::FOLDER_TRASH_ID,
            'readed'   => 1,
        ]);

        $countOutbox = MailBox::model()->countByAttributes([
            'sim_id'   => $this->simulation->id,
            'group_id' => MailBox::FOLDER_OUTBOX_ID,
        ]);

        // проверяем, вдруг пользователь не пользуется почтой?
        if (($countInboxRead + $countTrashRead < 10) || ($countOutbox < 5)) {
            return array(
                $behave_3311->getTypeScaleSlug() => 0,
                'obj'                            => $behave_3311,
                'case'                           => 1, // 'case' - option for test reasons only
            );
        }

        $workWithMailTotalDuration = 0; // seconds
        $mailSessionsTotalAmount = 0;
        $mailSessionsIsOpen = false;
        $currentSessionLegAction = false;

        $notMailLegType = [
            ActivityAction::LEG_TYPE_DOCUMENTS,
            ActivityAction::LEG_TYPE_MANUAL_DIAL,
            ActivityAction::LEG_TYPE_SYSTEM_DIAL,
        ];

        // обработка LogActivityActionAggregated
        foreach ($this->simulation->log_activity_actions_aggregated as $logItem) {
                if ($logItem->isMail()) {
                    $workWithMailTotalDuration += $logItem->getDurationInSeconds();
                }
                // check sessions from 11:00
                list($hours) = explode(':', $logItem->start_time);
                if ($hours < 11) {
                    continue;
                }

                if (false == $mailSessionsIsOpen &&
                    $logItem->isMail() &&
                    false === in_array($logItem->activityAction->activity->category_id, [0,1,2])) {
                    $mailSessionsIsOpen = true;
                    $mailSessionsTotalAmount++;
                }

                if (true === $mailSessionsIsOpen &&
                    in_array($logItem->leg_type, $notMailLegType)) {
                    $mailSessionsIsOpen = false;
                }
        }

        // проверяем что пользователь читал почту более 90 минут - это плохо
        if (90*60 < $workWithMailTotalDuration) {
            return array(
                $behave_3311->getTypeScaleSlug() => 0,
                'obj'                            => $behave_3311,
                'case'                           => 2, // 'case' - option for test reasons only
            );
        }

        // редко читает почту
        if ($mailSessionsTotalAmount < 2) {
            return array(
                $behave_3311->getTypeScaleSlug() => 0,
                'obj'                            => $behave_3311,
                'case'                           => 3, // 'case' - option for test reasons only
            );
        }

        // часто читает почту
        if (3 < $mailSessionsTotalAmount) {
            return array(
                $behave_3311->getTypeScaleSlug() => 0,
                'obj'                            => $behave_3311,
                'case'                           => 4, // 'case' - option for test reasons only
            );
        }

        // часто читает почту
        if (1 < $mailSessionsTotalAmount && $mailSessionsTotalAmount < 4) {
            $value = 0;

            if ($workWithMailTotalDuration <= 60*60) {
                $value = $behave_3311->scale;
            }

            if (60*60 < $workWithMailTotalDuration && $workWithMailTotalDuration <= 75*60) {
                $value = $behave_3311->scale*(2/3);
            }

            if (75*60 < $workWithMailTotalDuration && $workWithMailTotalDuration <= 90*60) {
                $value = $behave_3311->scale*(1/3);
            }

            return array(
                $behave_3311->getTypeScaleSlug() => $value,
                'obj'                            => $behave_3311,
                'case'                           => 5, // 'case' - option for test reasons only
            );
        }

        // сюда программа дойти не должна - но пусть хоть 0 вернёт, на всякий случай
        return array(
            $behave_3311->getTypeScaleSlug() => 0,
            'obj'                            => $behave_3311,
            'case'                           => 0, // 'case' - option for test reasons only
        );
    }

    public function check_3332()
    {
        $behave_3332 = $this->simulation->game_type->getHeroBehaviour(['code' => '3332']);

        if (null === $behave_3332) {
            return null;
        }

        $totalRightMsWithCopies = 0;

        $rightMss = MailTemplate::model()
            ->with('subject_obj')
            ->findAll(sprintf(
                " t.code LIKE 'MS%s' AND subject_obj.wr = 'R' AND t.scenario_id = %s",
                '%',
                $this->simulation->scenario_id
            ));

        foreach ($rightMss as $rightMs) {
            if (0 < MailTemplateCopy::model()->count(sprintf('mail_id = %s ', $rightMs->id))) {
                $totalRightMsWithCopies++;
            }
        }

        $usedMsCodes = [];
        $values = [];

        foreach ($this->userOutboxEmails as $outboxEmailData) {
            $email = $outboxEmailData->email;

            if ($email->isRight() && $email->isMS()) {

                // 1. get sorted TemplateCopy ids {
                $emailTemplateCopyCharacterIds = [];

                $copies = MailTemplateCopy::model()
                    ->findAllByAttributes(['mail_id' => $email->template->id]);
                foreach ($copies as $emailCopyCharacter) {
                    $emailTemplateCopyCharacterIds[] = $emailCopyCharacter->receiver_id;
                }

                sort($emailTemplateCopyCharacterIds);
                // 1. get sorted TemplateCopy ids }

                // 2. get sorted Copy ids {
                $emailCopyCharacterIds = [];

                $copies = MailCopy::model()
                    ->findAllByAttributes(['mail_id' => $email->id]);
                foreach ($copies as $emailCopyCharacter) {
                    $emailCopyCharacterIds[] = $emailCopyCharacter->receiver_id;
                }

                sort($emailCopyCharacterIds);
                // 2. get sorted Copy ids }

                if (0 == count ($emailTemplateCopyCharacterIds)) {
                    if (0 == count ($emailCopyCharacterIds)) {
                        $values[] = [
                            'code'  => $email->coincidence_mail_code,
                            'value' => 0,
                        ];
                    } else {
                        $values[] = [
                            'code'  => $email->coincidence_mail_code,
                            'value' => 0,
                        ];
                    }
                } else {
                    if ($emailTemplateCopyCharacterIds == $emailCopyCharacterIds) {
                        if (true == in_array($email->coincidence_mail_code, $usedMsCodes)) {
                            $values[] = [
                                'code'  => $email->coincidence_mail_code,
                                'value' => -1,
                            ];
                        } else {
                            $values[] = [
                                'code'  => $email->coincidence_mail_code,
                                'value' => 1,
                            ];
                        }
                    } else {
                        $values[] = [
                            'code'  => $email->coincidence_mail_code,
                            'value' => -1,
                        ];
                    }
                }

                $usedMsCodes[] = $email->coincidence_mail_code;
            }
        }

        $totalValue = 0;
        foreach ($values as $value) {
            $totalValue += $value['value'];
        }

        if ($totalValue < 0) {
            $totalValue = 0;
        }

        $totalValue = $totalValue/$totalRightMsWithCopies;

        return array(
            $behave_3332->getTypeScaleSlug() => $totalValue*$behave_3332->scale,
            'obj'                            => $behave_3332,
        );
    }

    /**
     * Codes of point codes that must be calculated in specific way
     * @return array
     */
    public function getExceptionPointCodes()
    {
        return array(
            '3313', '3322', '3323', '3324', '3325', '3326'
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
        $taskWays = MailTask::model()->byMailId($mailTemplateId)->byWrongRight('R')->findAll();
       
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
     * @param MailBox $email
     * @return boolean
     */
    private function isInbox($email)
    {
        return ($this->inboxEmailFolderId === (int)$email->group_id);
    }
    
    /**
     * @param MailBox $email
     * @return boolean
     */
    private function isInTrash($email)
    {
        return ($this->inboxEmailTrashFolderId === (int)$email->group_id);
    }
    
    /**
     * @param MailBox $email
     * @return boolean
     */
    private function isOutbox($email)
    {
        return ($this->outboxEmailFolderId === (int)$email->group_id);
    }
}

