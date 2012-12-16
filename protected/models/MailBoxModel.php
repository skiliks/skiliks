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
        return 0 != $this->sending_time;
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
            'subject' => array(self::BELONGS_TO, 'User', 'subject_id')
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


