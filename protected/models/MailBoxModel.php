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
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxModel extends CActiveRecord
{
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
     * @var string
     */
    public $subject;
    
    /**
     * @var integer, real Unix epoch time, in seconds
     */
    public $sending_date;
    
    /**
     * @var integer, real Unix epoch time, in seconds
     */
    public $receiving_date;
    
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
     * In minutes from 00:00 game day
     * @var int
     */
    public $sending_time;
    
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
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
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
        return 3 == $this->group_id;
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
            'subject_obj' => array(self::BELONGS_TO, 'MailThemesModel', 'subject_id'),
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

    public function getCharacterTheme() {
        $main_subject = MailThemesModel::model()->findByAttributes(array(
            'name' => $this->subject_obj->name,
            'sim_id' => null
        ));
        return MailCharacterThemesModel::model()->find(
            '(character_id=:sender_id OR character_id=:receiver_id) AND theme_id=:subject_id',
            array(
                'sender_id' => $this->sender_id,
                'receiver_id' => $this->receiver_id,
                'subject_id' => $main_subject->primaryKey,

        ));
    }
    
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


