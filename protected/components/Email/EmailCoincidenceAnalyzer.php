<?php

/**
 *
 * @author slavka
 */
class EmailCoincidenceAnalyzer
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
     * @param bool $strict Allow only sent messages
     * @return array
     */
    public function checkCoincidence($strict = true)
    {
        $result = array(
            'full'               => '-',
            'part1'              => '-',
            'part2'              => '-',
            'has_concidence'     => 0,
            'result_code'        => null,
            'result_template_id' => null,
            'result_type'        => null,
        ); 
        
        // not sended email can`t has any coinsidence by business logic
        if ($strict && false === $this->userEmail->isSended()) {
            return $result;
        }

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
                $mailAttachId[] = $attach->file_id;
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
            
           $this->emailTemplatesByCodeFull[$indexFull]   = $mailTemplate;
           $this->emailTemplatesByCodePart1[$indexPart1] = $mailTemplate;
           $this->emailTemplatesByCodePart2[$indexPart2] = $mailTemplate;
           
           unset($mailRecipientId);
           unset($mailCopyId);
           unset($mailAttachId);
           unset($indexFull);
           unset($indexPart1);
           unset($indexPart2);
        }
        
        // -----
        
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

        foreach (MailCopiesModel::model()->byMailId($this->userEmail->id)->findAll() as $copy) {
            $mailCopyId[] = $copy->receiver_id;
        }
        
        // mailCopyId }

        // mailAttachmentId {
        $mailAttachId = array();
        foreach (MailAttachmentsModel::model()->byMailId($this->userEmail->id)->findAll() as $attach) {
            $doc = MyDocumentsModel::model()->byId($attach->file_id)->find();
            if (null !== $doc) {
                $mailAttachId[] = $doc->template_id;
            }
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
        
        if (isset($this->emailTemplatesByCodeFull[$indexFull])) {
            $result['full'] = $this->emailTemplatesByCodeFull[$indexFull]->code;
            $result['result_code'] = $this->emailTemplatesByCodeFull[$indexFull]->code;
            $result['result_template_id'] = $this->emailTemplatesByCodeFull[$indexFull]->id;
            $result['has_concidence'] = 1;
            $result['result_type'] = MailBoxModel::COINCIDENCE_FULL;
        }elseif (isset($this->emailTemplatesByCodePart1[$indexPart1])) {
            $result['part1'] = $this->emailTemplatesByCodePart1[$indexPart1]->code;
            $result['result_code'] = $this->emailTemplatesByCodePart1[$indexPart1]->code;
            $result['result_template_id'] = $this->emailTemplatesByCodePart1[$indexPart1]->id;
            $result['has_concidence'] = 1;
            $result['result_type'] = MailBoxModel::COINCIDENCE_PART_1;
        }elseif (isset($this->emailTemplatesByCodePart2[$indexPart2])) {
            $result['part2'] = $this->emailTemplatesByCodePart2[$indexPart2]->code;
            $result['result_code'] = $this->emailTemplatesByCodePart2[$indexPart2]->code;
            $result['result_template_id'] = $this->emailTemplatesByCodePart2[$indexPart2]->id;
            $result['has_concidence'] = 1;
            $result['result_type'] = MailBoxModel::COINCIDENCE_PART_2;
        }
        
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
        sort($recipientsIds);
        sort($copyCharacterIds);
        sort($attachmentIds);
        
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
        sort($recipientsIds);
        sort($attachmentIds);
        
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
        sort($attachmentIds);
        
        return sprintf(
            '%s_%s_%s',
            $firstRecipientId,
            $subjectId,
            implode('-', $attachmentIds)
        );
    }
}

