<?php



/**
 * Шаблон набора писем. Копируется в рамках симуляции в почтовый ящик польщзователя.
 *
 * @property string type_of_importance
 * @property string import_id
 * @property CommunicationTheme subject_obj
 * @property ActivityParent[] termination_parent_actions
 * @property MailAttachmentTemplate[] attachments
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailTemplate extends CActiveRecord implements IGameAction
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_group.id
     * @var integer
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
    public $message;
    
    /**
     * mail_themes.id
     * @var int
     */
    public $subject_id;
    
    /**
     * Code, 'M1', 'MS8' ...
     * @var string
     */
    public $code;

    /**
     * @var int
     */
    public $type; // ?

    /**
     * @var string
     * 'none', 'spam', '2_min', 'plan', 'info', 'first_category', ...
     */
    public $type_of_impportance;

    /**
     * @var string
     */
    public $flag_to_switch;

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailTemplate
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'mail_template';
    }
    
    /**
     * Выбрать письмо с заданным кодом
     * @param string $code
     * @return MailTemplate 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
    
    /**
     * @param string $ids
     * @return \MailTemplate
     */
    public function byIdsNotIn($ids)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " `id` NOT IN ({$ids})"
        ));
        return $this;
    }    
    
    /**
      * @param integer $receiverId
     * @return \MailTemplate
     */
    public function byReceiverId($receiverId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "receiver_id = '{$receiverId}'"
        ));
        return $this;
    }
    
    /**
     * @param integer $subjectId
     * @return \MailTemplate
     */
    public function bySubjectId($subjectId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "subject_id = '{$subjectId}'"
        ));
        return $this;
    }
    
    /**
     * Returns templates for outbox letters
     * @return MailTemplate 
     */
    public function byMS()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code like 'MS%'"
        ));
        return $this;
    }

    public function relations()
    {
        return [
            'termination_parent_actions' => [self::HAS_MANY, 'ActivityParent', 'mail_id'],
            'subject_obj'                => [self::BELONGS_TO, 'CommunicationTheme', 'subject_id'],
            'attachments'                => [self::HAS_MANY, 'MailAttachmentTemplate', 'mail_id']
        ];
    }
}


