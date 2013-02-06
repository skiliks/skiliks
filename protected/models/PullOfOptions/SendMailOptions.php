<?php

/**
 * @author slavka
 */
class SendMailOptions
{
     public $simulation = NULL;
     public $senderId   = NULL;
    /**
     * @var MailTemplateModel $messageId
     */
    public $messageId  = NULL;
     public $time       = NULL;
     public $fileId     = NULL;
     public $subject_id  = NULL;
     
     public $copies     = array();
     public $phrases    = array();
     
     private $letterType = NULL;
     
     private $recipients  = array();
     
     /**
      * @return bool
      */
     public function isReply()
     {
         return (MailBoxModel::TYPE_REPLY === $this->letterType ||
                 MailBoxModel::TYPE_REPLY === $this->letterType);
     }
     
     /**
      * @return bool
      */
     public function isValidMessageId()
     {
         return (true === is_numeric($this->messageId) && 0 < $this->messageId);
     }

     /**
      * @return bool
      */
     public function isValidRecipientsArray()
     {
         return (0 < count($this->recipients));
     }

     /**
      * @param string $letterType
      * @return \SendMailOptions
      */
     public function setLetterType($letterType)
     {
         $this->letterType = (string)$letterType;
         
         return $this;
     }
     
     /**
      * @return string
      */
     public function getLetterType()
     {
         return $this->letterType;
     }
     
     /**
      * @param array $recipients
      * @return \SendMailOptions
      */
     public function setRecipientsArray($recipients)
     {
         $this->recipients = explode(',', (string)$recipients);
         
         // remove "" items
         $this->recipients = array_filter($this->recipients);
         
         return $this;
     }
     
     /**
      * @return string
      */
     public function getRecipientsArray()
     {
         return $this->recipients;
     }
}

