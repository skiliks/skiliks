<?php

/**
 * @author slavka
 */
class SendMailOptions
{
    public $simulation = NULL;
    public $senderId;

    public $groupId    = MailBox::FOLDER_OUTBOX_ID;
    /**
     * @var MailTemplate $messageId
     */
    public $messageId  = NULL;
    public $time       = NULL;
    public $fileId     = NULL;
    public $subject_id  = NULL;

    // is this email created by press 'Reply all'
    public $is_reply_all = false;

    public $copies     = array();
    public $phrases    = array();
     
    private $letterType = NULL;
     
    private $recipients  = array();

    const REPLY_ALL_FRONTEND_SCREEN_ALIAS = 'SCREEN_WRITE_REPLY_ALL';

    public function __construct()
    {
        $this->senderId = Character::model()->findByAttributes(['code' => Character::HERO_ID]);

    }
     
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

