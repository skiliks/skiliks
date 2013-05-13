<?php



/**
 * Собственно сам почтовый ящик в рамках конкретной симуляции. Все что сюда
 * попадает то и видит пользователь в своей симуляции.
 *
 * @property mixed group_id
 * @property mixed sender_id
 * @property mixed subject_id
 * @property int receiver_id
 * @property mixed sending_date
 * @property int readed
 * @property mixed sim_id
 * @property mixed letter_type
 * @property MailTemplate template
 * @property Simulation simulation
 * @property CommunicationTheme subject_obj
 * @property MailAttachment $attachment
 * @property MailBox $parentMail
 *
 */
class MailBox extends CActiveRecord
{
    const COINCIDENCE_FULL   = 'full';
    const COINCIDENCE_PART_1 = 'part1';
    const COINCIDENCE_PART_2 = 'part2';
    
    const FOLDER_INBOX_ID  = 1;
    const FOLDER_DRAFTS_ID = 2;
    const FOLDER_OUTBOX_ID = 3;
    const FOLDER_TRASH_ID  = 4;
    const FOLDER_NOT_RECEIVED_EMAILS_ID  = 5;

    const FOLDER_INBOX_ALIAS  = 'inbox';
    const FOLDER_DRAFTS_ALIAS = 'drafts';
    const FOLDER_OUTBOX_ALIAS = 'outbox';
    const FOLDER_TRASH_ALIAS  = 'trash';
    const FOLDER_NOT_RECEIVED_ALIAS  = 'not received';

    const TYPE_FORWARD   = 'forward';
    const TYPE_REPLY     = 'reply';
    const TYPE_REPLY_ALL = 'replyAll';

    /**
     * TODO: make THIS private
     * @var array<int>
     */
    public static $folderIdToAlias = array(
        self::FOLDER_INBOX_ID  => self::FOLDER_INBOX_ALIAS,
        self::FOLDER_DRAFTS_ID => self::FOLDER_DRAFTS_ALIAS,
        self::FOLDER_OUTBOX_ID => self::FOLDER_OUTBOX_ALIAS,
        self::FOLDER_TRASH_ID  => self::FOLDER_TRASH_ALIAS,
        self::FOLDER_NOT_RECEIVED_EMAILS_ID => self::FOLDER_NOT_RECEIVED_ALIAS
    );
    
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_template.id
     * @var int
     */
    public $template_id;
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;
    
    /**
     * mail_group.id
     * @var int
     */
    public $group_id;
    
    /**
     * characters.id
     * @var int
     */
    public $sender_id;    
    
    /**
     * characters.id
     * @var int
     */
    public $receiver_id;

    /**
     * @var datetime
     */
    public $sent_at;
    
    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $message;
    
    /**
     * is readed
     * @var int (bool)
     */
    public $readed; 
    
    /**
     * mail_themes.id
     * @var int
     */
    public $subject_id;
    
    /**
     * Code, 'M1', 'MS8' ...
     * 
     * MS - mail sended by hero
     * MY - ? mail sended by hero yesterday
     * M - mail received during game
     * MY - mail in inbox when game starts
     * 
     * @var string
     */
    public $code;

    /**
     * @var int
     */
    public $type; // ?
    
    /**
     * is planed
     * @var int (bool)
     */
    public $plan; 
    
    /**
     * is replied
     * @var int (bool)
     */
    public $reply; 
    
    /**
     * mail_box.id
     * If current email is reply, message_id = id of source email
     * @var int
     */
    public $message_id;
    
    /**
     * @var string | NULL
     */
    public $coincidence_type;
    
    /**
     * Like MS1, MS2 etc.
     * @var string
     */
    public $coincidence_mail_code;


    /** ------------------------------------------------------------------------------------------------------------ **/
    
    public function markReplied()
    {
        $this->reply = 1;
    }

    /**
     * is Email has "R" theme
     * @return bool
     */
    public function isRight()
    {
        return CommunicationTheme::SLUG_RIGHT == $this->subject_obj->wr;
    }

    /**
     * @return bool
     */
    public function isMS()
    {
        return null !== $this->coincidence_mail_code;
    }

    /**
     * @return boolean
     */
    public function isSended() {
        return MailBox::FOLDER_OUTBOX_ID == $this->group_id;
    }

    /**
     * @return boolean
     */
    public function isPlanned() {
        return true === (bool)$this->plan;
    }

    public function getCopyCharacterCodes()
    {
        $copies = MailCopy::model()->findAllByAttributes(['mail_id' => $this->id]);

        $res = [];

        foreach ($copies as $copy) {
            $res[] = $copy->recipient->code;
        }

        return $res;
    }

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @return bool
     */
    public function isHasCoincidence()
    {
        return null === $this->coincidence_mail_code;
    }

    /**
     * @param string $type, self::COINCIDENCE_FULL, self::COINCIDENCE_PART_1, self::COINCIDENCE_PART_2
     * @return bool
     */
    public function isHasCoincidenceByType($type)
    {
        if (false === in_array($type, $this->getCoincidenceTypes())) {
            throw new Exception(sprintf('Wrong mail coincidence type %s.', $type));
        }        
        
        return $type === $this->coincidence_type;
    }    
    
    public function getCoincidenceTypes()
    {
        return array(self::COINCIDENCE_FULL, self::COINCIDENCE_PART_1, self::COINCIDENCE_PART_2);
    }

    /**
     *
     * @param string $className
     * @return MailBox 
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'subject_obj' => array(self::BELONGS_TO, 'CommunicationTheme', 'subject_id'),
            'template'    => array(self::BELONGS_TO, 'MailTemplate', 'template_id'),
            'simulation'    => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'attachment'    => array(self::HAS_ONE, 'MailAttachment', 'mail_id'),
            'parentMail'    => array(self::BELONGS_TO, 'MailBox', 'message_id'),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'mail_box';
    }

    /**
     * Выбрать по заданной папкe
     * @param int $folderId
     * @return MailBox 
     */
    public function byFolder($folderId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "group_id = {$folderId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному получателю
     * @param int $receiverId
     * @return MailBox 
     */
    public function byReceiver($receiverId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "receiver_id = {$receiverId}"
        ));
        return $this;
    }

    /**
     * Выбрать по отправителю
     * @param int $senderId
     * @return MailBox 
     */
    public function bySender($senderId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sender_id = {$senderId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать конкретное письмо
     * @param int $id
     * @return MailBox 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать письмо по коду
     * @param string $code
     * @return MailBox 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
    
    /**
     * Сортировать по заданному полю в заданном направлении
     * @param string $fieldName
     * @param string $direction
     * @return MailBox 
     */
    public function orderBy($fieldName, $direction)
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "$fieldName $direction"
        ));
        return $this;
    }

    public function getGroupName() {
        return self::$folderIdToAlias[$this->group_id];
    }
}


