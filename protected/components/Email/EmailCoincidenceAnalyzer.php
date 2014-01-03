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
        $this->userEmail = MailBox::model()->findByPk($userEmailId);
        
        return (null !== $this->userEmail);
    }    

    /**
     * 
     * @return mixed array
     */
    public function checkCoincidence()
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
        if (false === $this->userEmail->isSended()) {
            return $result;
        }

        $mailTemplates = $this->userEmail->simulation->game_type->getMailTemplates([
            'receiver_id' => $this->userEmail->receiver_id,
            'theme_id'    => $this->userEmail->theme_id,
            'mail_prefix' => $this->userEmail->mail_prefix
        ]);

        foreach ($mailTemplates as $mailTemplate) {
            if (!preg_match('/MS\d+/',$mailTemplate->code)) {
                continue;
            }
            // mailRecipientId{
            $mailRecipientId = array($mailTemplate->receiver_id);
            foreach (MailTemplateRecipient::model()->findAllByAttributes(['mail_id' => $mailTemplate->id]) as $recipient) {
                if (false == in_array($recipient->receiver_id, $mailRecipientId)) {
                    $mailRecipientId[] = $recipient->receiver_id;
                }
            }   
            // mailRecipientId }
            
            // mailCopyId {
            $mailCopyId = array();
            foreach (MailTemplateCopy::model()->findAllByAttributes(['mail_id' => $mailTemplate->id]) as $copy) {
                $mailCopyId[] = $copy->receiver_id;
            }
            // mailCopyId }
            
            // mailAttachmentId {
            $mailAttachId = array();
            $mailAttachmentTemplates = MailAttachmentTemplate::model()->findAllByAttributes([
                'mail_id' => $mailTemplate->id]
            );
            foreach ($mailAttachmentTemplates as $attach) {
                $mailAttachId[] = $attach->file_id;
            }
            // mailAttachmentId }
            
            $indexFull = $this->getMailCodeFullConsidence(
                $mailRecipientId,
                $mailCopyId, 
                $mailTemplate->theme_id,
                $mailTemplate->mail_prefix,
                $mailAttachId);
            
            $indexPart1 = $this->getMailCodePart1Considence(
                $mailRecipientId,
                $mailTemplate->theme_id,
                $mailTemplate->mail_prefix,
                $mailAttachId);
            
            $indexPart2 = $this->getMailCodePart2Considence(
                $mailTemplate->receiver_id,
                $mailTemplate->theme_id,
                $mailTemplate->mail_prefix,
                $mailAttachId);
            
           $this->emailTemplatesByCodeFull[$indexFull]   = $mailTemplate;
           $this->emailTemplatesByCodePart1[$indexPart1] = $mailTemplate;
           $this->emailTemplatesByCodePart2[$indexPart2] = $mailTemplate;

           var_dump($indexFull);

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
        foreach (MailRecipient::model()->findAllByAttributes(['mail_id' => $this->userEmail->id]) as $recipient) {
            if (false == in_array($recipient->receiver_id, $mailRecipientId)) {
                $mailRecipientId[] = $recipient->receiver_id;
            }
        }    
        // mailRecipientId }

        // mailCopyId {
        $mailCopyId = array();

        foreach (MailCopy::model()->findAllByAttributes(['mail_id' => $this->userEmail->id]) as $copy) {
            $mailCopyId[] = $copy->receiver_id;
        }
        // mailCopyId }

        // mailAttachmentId {
        $mailAttachId = array();
        foreach (MailAttachment::model()->findAllByAttributes(['mail_id' => $this->userEmail->id]) as $attach) {
            $doc = MyDocument::model()->findByPk($attach->file_id);
            if (null !== $doc) {
                $mailAttachId[] = $doc->template_id;
            }
        }
        // mailAttachmentId }
        
        $indexFull = $this->getMailCodeFullConsidence(
            $mailRecipientId,
            $mailCopyId, 
            $this->userEmail->theme_id,
            $this->userEmail->mail_prefix,
            $mailAttachId
        );

        $indexPart1 = $this->getMailCodePart1Considence(
            $mailRecipientId,
            $this->userEmail->theme_id,
            $this->userEmail->mail_prefix,
            $mailAttachId
        );

        $indexPart2 = $this->getMailCodePart2Considence(
            $this->userEmail->receiver_id,
            $this->userEmail->theme_id,
            $this->userEmail->mail_prefix,
            $mailAttachId
        );

        unset($mailRecipientId);
        unset($mailCopyId);
        unset($mailAttachId);    
        
        // check
        if (isset($this->emailTemplatesByCodeFull[$indexFull])) {
            $result['full'] = $this->emailTemplatesByCodeFull[$indexFull]->code;
            $result['result_code'] = $this->emailTemplatesByCodeFull[$indexFull]->code;
            $result['result_template_id'] = $this->emailTemplatesByCodeFull[$indexFull]->id;
            $result['has_concidence'] = 1;
            $result['result_type'] = MailBox::COINCIDENCE_FULL;
        }elseif (isset($this->emailTemplatesByCodePart1[$indexPart1])) {
            $result['part1'] = $this->emailTemplatesByCodePart1[$indexPart1]->code;
            $result['result_code'] = $this->emailTemplatesByCodePart1[$indexPart1]->code;
            $result['result_template_id'] = $this->emailTemplatesByCodePart1[$indexPart1]->id;
            $result['has_concidence'] = 1;
            $result['result_type'] = MailBox::COINCIDENCE_PART_1;
        }elseif (isset($this->emailTemplatesByCodePart2[$indexPart2])) {
            $result['part2'] = $this->emailTemplatesByCodePart2[$indexPart2]->code;
            $result['result_code'] = $this->emailTemplatesByCodePart2[$indexPart2]->code;
            $result['result_template_id'] = $this->emailTemplatesByCodePart2[$indexPart2]->id;
            $result['has_concidence'] = 1;
            $result['result_type'] = MailBox::COINCIDENCE_PART_2;
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
    private function getMailCodeFullConsidence($recipientsIds, $copyCharacterIds, $themeId, $mailPrefix, $attachmentIds)
    {
        sort($recipientsIds);
        sort($copyCharacterIds);
        sort($attachmentIds);
        
        return sprintf(
            '%s_%s_%s_%s_%s',
            implode('-', $recipientsIds),
            implode('-', $copyCharacterIds),
            $themeId,
            $mailPrefix,
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
    private function getMailCodePart1Considence($recipientsIds, $themeId, $mailPrefix, $attachmentIds)
    {
        sort($recipientsIds);
        sort($attachmentIds);
        
        return sprintf(
            '%s_%s_%s_%s',
            implode('-', $recipientsIds),
            $themeId,
            $mailPrefix,
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
    private function getMailCodePart2Considence($firstRecipientId, $themeId, $mailPrefix, $attachmentIds)
    {
        sort($attachmentIds);
        
        return sprintf(
            '%s_%s_%s_%s',
            $firstRecipientId,
            $themeId,
            $mailPrefix,
            implode('-', $attachmentIds)
        );
    }
}

