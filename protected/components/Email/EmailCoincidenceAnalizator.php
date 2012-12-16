<?php

/**
 *
 * @author slavka
 */
class EmailCoincidenceAnalizator 
{
    /**
     * @var MailBox
     */
    private $userEmail;
    
    /**
     * @var array of string, code. Like 'MS1', 'MS2', ...
     */
    private $emailTemplatesByCodeFull;
    
    /**
     * @var array of string, code. Like 'MS1', 'MS2', ...
     */
    private $emailTemplatesByCodePart1;
    
    /**
     * @var array of string, code. Like 'MS1', 'MS2', ...
     */
    private $emailTemplatesByCodeRart2;

    /**
     * @param index $userEmailId
     * 
     * @return boolean
     */
    public function setUserEmail($userEmailId)
    {
        $this->userEmail = null;
        $this->userEmail = MailBoxModel::model()->findByPk($userEmailId);
        
        return (null !== $this->userEmail);
    }    

    /**
     * 
     * @return mixed array
     */
    public function checkCoinsidence() 
    {
         //Yii::log(' ------------------------------- TEMPLATES --------------------------------');
        $templates = MailTemplateModel::model()
                ->byReceiverId($this->userEmail->receiver_id)
                ->bySubjectId($this->userEmail->subject_id)
                ->findAll();
//        Yii::log('Templates counter: '.count($templates));
//        Yii::log('Templates receiver_id: '.$this->userEmail->receiver_id);
//        Yii::log('Templates subject_id: '.$this->userEmail->subject_id);
//        Yii::log('userEmail: '.  serialize($this->userEmail));
        
            foreach (MailTemplateModel::model()
                ->byMS()
                ->byReceiverId($this->userEmail->receiver_id)
                ->bySubjectId($this->userEmail->subject_id)
                ->findAll() as $mailTemplate) {
            // mailRecipientId{
            $mailRecipientId = array($mailTemplate->receiver_id);
            foreach (MailReceiversTemplateModel::model()->byMailId($mailTemplate->id)->findAll() as $recipient) {
                if (false == in_array($recipient->receiver_id, $mailRecipientId)) {
                    $mailRecipientId[] = $recipient->receiver_id;
                }
            }   
            // mailRecipientId }
            
            // mailCopyId {
            $mailCopyId = array();
            foreach (MailCopiesTemplateModel::model()->byMailId($mailTemplate->id)->findAll() as $copy) {
                $mailCopyId[] = $copy->receiver_id;
            }
            // mailCopyId }
            
            // mailAttachmentId {
            $mailAttachId = array();
            foreach (MailAttachmentsTemplateModel::model()->byMailId($mailTemplate->id)->findAll() as $attach) {
                $mailAttachId[] = $attach->id;
            }
            // mailAttachmentId }
            
            $indexFull = $this->getMailCodeFullConsidence(
                $mailRecipientId,
                $mailCopyId, 
                $mailTemplate->subject_id, 
                $mailAttachId);
            
            $indexPart1 = $this->getMailCodePart1Considence(
                $mailRecipientId,
                $mailTemplate->subject_id, 
                $mailAttachId);
            
            $indexPart2 = $this->getMailCodePart2Considence(
                $mailTemplate->receiver_id,
                $mailTemplate->subject_id, 
                $mailAttachId);
            
           $this->emailTemplatesByCodeFull[$indexFull]   = $mailTemplate->code;
           $this->emailTemplatesByCodePart1[$indexPart1] = $mailTemplate->code;
           $this->emailTemplatesByCodePart2[$indexPart2] = $mailTemplate->code;
           unset($mailRecipientId);
           unset($mailCopyId);
           unset($mailAttachId);
        }
        
        // -----
        
        //Yii::log(' -------------------------------USER --------------------------------');
        
        // mailRecipientId{
        $mailRecipientId = array($this->userEmail->receiver_id);
        foreach (MailReceiversModel::model()->byMailId($this->userEmail->id)->findAll() as $recipient) {
            if (false == in_array($recipient->receiver_id, $mailRecipientId)) {
                $mailRecipientId[] = $recipient->receiver_id;
            }
        }    
        // mailRecipientId }

        // mailCopyId {
        $mailCopyId = array();
        //Yii::log('*** COPIES ***');
        $r = MailCopiesModel::model()->byMailId($this->userEmail->id)->findAll();
        Yii::log('User email copies: '. count($r));
        foreach (MailCopiesModel::model()->byMailId($this->userEmail->id)->findAll() as $copy) {
            $mailCopyId[] = $copy->receiver_id;
        }
        // mailCopyId }

        // mailAttachmentId {
        $mailAttachId = array();
        foreach (MailAttachmentsModel::model()->byMailId($this->userEmail->id)->findAll() as $attach) {
            $mailAttachId[] = $attach->id;
        }
        // mailAttachmentId }
        
        $indexFull = $this->getMailCodeFullConsidence(
            $mailRecipientId,
            $mailCopyId, 
            $this->userEmail->subject_id, 
            $mailAttachId);

        $indexPart1 = $this->getMailCodePart1Considence(
            $mailRecipientId,
            $this->userEmail->subject_id, 
            $mailAttachId);

        $indexPart2 = $this->getMailCodePart2Considence(
            $this->userEmail->receiver_id,
            $this->userEmail->subject_id, 
            $mailAttachId);
        
        unset($mailRecipientId);
        unset($mailCopyId);
        unset($mailAttachId);        
        
        // check 
        $result = array(
            'full'           => '-',
            'part1'          => '-',
            'part2'          => '-',
            'has_concidence' => 0,
        );  
        
         //Yii::log(' ------------------------------- RESULTS --------------------------------');
        
        if (isset($this->emailTemplatesByCodeFull[$indexFull])) {
            $result['full'] = $this->emailTemplatesByCodeFull[$indexFull];
            if ($this->userEmail->isSended()) {
                $result['has_concidence'] = 1;
            }
        }elseif (isset($this->emailTemplatesByCodePart1[$indexPart1])) {
            $result['part1'] = $this->emailTemplatesByCodePart1[$indexPart1];
            if ($this->userEmail->isSended()) {
                $result['has_concidence'] = 1;
            }
        }elseif (isset($this->emailTemplatesByCodePart2[$indexPart2])) {
            $result['part2'] = $this->emailTemplatesByCodePart2[$indexPart2];
            if ($this->userEmail->isSended()) {
                $result['has_concidence'] = 1;
            }
        }
        
        /*Yii::log('EmailCoincidenceAnalizator emailTemplatesByCodeFull: '.serialize($this->emailTemplatesByCodeFull));
        Yii::log('EmailCoincidenceAnalizator emailTemplatesByCode1: '.serialize($this->emailTemplatesByCodePart1));
        Yii::log('EmailCoincidenceAnalizator emailTemplatesByCode2: '.serialize($this->emailTemplatesByCodePart2));
        Yii::log('EmailCoincidenceAnalizator indexFull: '.$indexFull);
        Yii::log('EmailCoincidenceAnalizator index1: '.$indexPart1);
        Yii::log('EmailCoincidenceAnalizator index2: '.$indexPart2);*/
        
        return $result;
    }
    
    /* --- system : ------------- */
    
    /**
     * Mail codes used to make compartion of 2 emails in one step
     */
    
    /**
     * @param array of integer $recipientsIds
     * @param array of integer $copyCharacterIds
     * @param integer $subjectId
     * @param array of integer $attachmentIds
     * 
     * @return string
     */
    private function getMailCodeFullConsidence($recipientsIds, $copyCharacterIds, $subjectId, $attachmentIds)
    {
        return sprintf(
            '%s_%s_%s_%s',
            implode('-', $recipientsIds),
            implode('-', $copyCharacterIds),
            $subjectId,
            implode('-', $attachmentIds)
        );
    }
    
    /**
     * @param array of integer $recipientsIds
     * @param integer $subjectId
     * @param array of integer $attachmentIds
     * 
     * @return string
     */
    private function getMailCodePart1Considence($recipientsIds, $subjectId, $attachmentIds)
    {
        return sprintf(
            '%s_%s_%s',
            implode('-', $recipientsIds),
            $subjectId,
            implode('-', $attachmentIds)
        );
    }
    
    /**
     * @param integer $firstRecipientId
     * @param integer $subjectId
     * @param array of integer $attachmentIds
     * 
     * @return string
     */
    private function getMailCodePart2Considence($firstRecipientId, $subjectId, $attachmentIds)
    {
        return sprintf(
            '%s_%s_%s',
            $firstRecipientId,
            $subjectId,
            implode('-', $attachmentIds)
        );
    }
}

