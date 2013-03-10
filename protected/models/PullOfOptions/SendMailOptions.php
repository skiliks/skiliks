<?php

/**
 * @author slavka
 */
class SendMailOptions
{
    public $simulation = NULL;
    public $senderId   = Character::HERO_ID;

    public $groupId    = MailBox::FOLDER_OUTBOX_ID;
    /**
     * @var MailTemplate $messageId
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
         return (MailBox::TYPE_REPLY === $this->letterType ||
                 MailBox::TYPE_REPLY === $this->letterType);
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
     public function setLetterType($letter_type)
     {
         $this->letterType = (string)$letter_type;
         
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

