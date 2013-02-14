<?php



/**
 * Собственно сам почтовый ящик в рамках конкретной симуляции. Все что сюда
 * попадает то и видит пользователь в своей симуляции.
 * 
 * Связана с моделями: Characters, Simulations, MailTemplateModel.
 *
 * @property mixed group_id
 * @property mixed sender_id
 * @property mixed subject_id
 * @property int receiver_id
 * @property mixed sending_date
 * @property int readed
 * @property mixed sim_id
 * @property MailTemplateModel template
 * @property mixed letter_type
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxModel extends CActiveRecord
{
    const COINCIDENCE_FULL   = 'full';
    const COINCIDENCE_PART_1 = 'part1';
    const COINCIDENCE_PART_2 = 'part2';
    
    const INBOX_FOLDER_ID  = 1;
    const DRAFTS_FOLDER_ID = 2;
    const OUTBOX_FOLDER_ID = 3;
    const TRASH_FOLDER_ID  = 4;
    const NOT_RECEIVED_EMAILS_GROUP_ID  = 5;

    const INBOX_FOLDER_ALIAS  = 'inbox';
    const DRAFTS_FOLDER_ALIAS = 'drafts';
    const OUTBOX_FOLDER_ALIAS = 'outbox';
    const TRASH_FOLDER_ALIAS  = 'trash';
    
    const TYPE_REPLY    = 'reply';
    const TYPE_REPLY_ALL = 'replyAll';
    
    public static $folderIdToAlias = array(
        self::INBOX_FOLDER_ID  => self::INBOX_FOLDER_ALIAS,
        self::DRAFTS_FOLDER_ID => self::DRAFTS_FOLDER_ALIAS,
        self::OUTBOX_FOLDER_ID => self::OUTBOX_FOLDER_ALIAS,
        self::TRASH_FOLDER_ID  => self::TRASH_FOLDER_ALIAS,
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
        if (false === in_array($type, $this->getConcidenceTypes())) {
            throw new Exception(sprintf('Wrong mail coincidence type %s.', $type));
        }        
        
        return $type === $this->coincidence_type;
    }    
    
    public function getConcidenceTypes()
    {
        return array(self::COINCIDENCE_FULL, self::COINCIDENCE_PART_1, self::COINCIDENCE_PART_2);
    }

    /**
     * @return boolean
     */
    public function isMS() {
        return preg_match("/MS\d+/", $this->code);
    }
    
    /**
     * @return boolean
     */
    public function isSended() {
        return MailBoxModel::OUTBOX_FOLDER_ID == $this->group_id;
    }

    /**
     * @return boolean
     */
    public function isPlanned() {
        return true === (bool)$this->plan;
    }

    /**
     *
     * @param string $className
     * @return MailBoxModel 
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    public function relations() {
        return array(
            'subject_obj' => array(self::BELONGS_TO, 'CommunicationTheme', 'subject_id'),
            'template' => array(self::BELONGS_TO, 'MailTemplateModel', 'template_id')
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
     * @return type
     */
    /*public function getCharacterTheme() {
        
        $main_subject = CommunicationTheme::model()->findByAttributes(array(
            'text' => $this->subject_obj->text
        ));

        // try to find subject for current simulation
        if (null === $main_subject) {
            $main_subject = CommunicationTheme::model()->findByAttributes(array(
                'id'   => $this->subject_obj->id,
            ));
        }
        
        return CommunicationTheme::model()->find(
            '(character_id=:sender_id OR character_id=:receiver_id) AND id=:subject_id',
            array(
                'sender_id'   => $this->sender_id,
                'receiver_id' => $this->receiver_id,
                'subject_id'  => $main_subject->primaryKey,
        ));
    }*/
    
    /**
     * Выбрать по заданной папкe
     * @param int $folderId
     * @return MailBoxModel 
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
     * @return MailBoxModel 
     */
    public function byReceiver($receiverId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "receiver_id = {$receiverId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать в рамках заданной симуляции
     * @param int $simId
     * @return MailBoxModel 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по отправителю
     * @param int $senderId
     * @return MailBoxModel 
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
     * @return MailBoxModel 
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
     * @return MailBoxModel 
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
     * @return MailBoxModel 
     */
    public function orderBy($fieldName, $direction)
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "$fieldName $direction"
        ));
        return $this;
    }
}


