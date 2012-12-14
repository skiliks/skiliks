<?php



/**
 * Шаблон набора писем. Копируется в рамках симуляции в почтовый ящик польщзователя.
 *
 * Связана с моделями:  MailThemesModel, Characters.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailTemplateModel extends CActiveRecord
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
     * In minutes from 00:00 game day
     * @var int
     */
    public $sending_time;
    
    /**
     * @var int
     */
    public $type; // ?
    
    /**
     * @var string 'hh:ii'
     */
    public $sending_time_str;    
    
    /**
     * @var string 'dd.mm.yyyy'
     */
    public $sending_date_str;
    
    /**
     * @var string
     * 'none', 'spam', '2_min', 'plan', 'info', 'first_category', ...
     */
    public $type_of_impportance;  
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailTemplateModel
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
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
     * @return MailTemplateModel 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
}


