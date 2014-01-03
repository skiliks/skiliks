<?php
/**
 * @property Simulation $simulation
 * @property integer $senderId
 * @property integer $groupId
 */
class SendMailOptions
{
    /**
     *
     */
    const REPLY_ALL_FRONTEND_SCREEN_ALIAS = 'SCREEN_WRITE_REPLY_ALL';

    /* @var Simulation $simulation */
    public $simulation = NULL;

    /* @var integer */
    public $senderId;

    /* @var integer */
    public $groupId    = MailBox::FOLDER_OUTBOX_ID;

    /* @var MailTemplate $messageId */
    public $messageId  = NULL;

    /* @var string $time */
    public $time       = NULL;

    /* @var integer $fileId */
    public $fileId  = NULL;

    /**
     * Id темы с theme
     * @var null
     */
    public $themeId = NULL;

    /**
     * Префикс письма, например re:, re:re: ...
     * @var null
     */
    public $mailPrefix = NULL;

    /**
     * Код конструктора например B1, TXT ...
     * @var null
     */
    public $constructorCode = NULL;

    /* @var integer $id */
    public $id         = NULL;

    /* var bool $is_reply_all, is this email created by press 'Reply all' */
    /**
     * @var bool
     */
    public $is_reply_all = false;

    /* @var array of ??? */
    public $copies     = array();

    /* @var array of ??? */
    public $phrases    = array();

    /* @var string $time */
    private $letterType = NULL;

    /* @var array of ??? */
    private $recipients  = array();

    /**
     * По умолчанию письмо отправляется от имени главного героя в данном сценарии
     *
     * @param Simulation $simulation
     */
    public function __construct(Simulation $simulation)
    {
        $this->simulation = $simulation;

        $this->senderId = $simulation->game_type->getCharacter([
            'code' => Character::HERO_CODE
        ])->getPrimaryKey();
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

