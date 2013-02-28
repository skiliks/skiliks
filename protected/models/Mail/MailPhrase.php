<?php



/**
 * Содержит набор фраз, которые соответствуют конкретному персонажу
 * Также есть связь по коду конструктора писем. Поле code например B1 W1
 * 
 * Связана с моделями: CommunicationTheme.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailPhrase extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_character-themes.id
     * @var int
     */
    public $character_theme_id; 
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var int
     */
    public $thrase_type;
    
    /**
     * Constructor code, 'B1','R1' ...
     * @var string
     */
    public $code;    
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailPhrase 
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
            return 'mail_phrases';
    }
    
    
    /**
     * Выбрать фразы по заданному набору
     * @param array $ids
     * @return MailPhrase 
     */
    public function byIds($ids)
    {
        if (count($ids) == 0) return $this;
        
        $ids = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному соответствию персонаж - тема
     * @param int $id
     * @return MailPhrase 
     */
    public function byCharacterThemes($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "character_theme_id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по типу фразы
     * @param int $type
     * @return MailPhrase 
     */
    public function byType($type)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "phrase_type = {$type}"
        ));
        return $this;
    }
    
    /**
     * Выбрать фразу по коду
     * @param string $code
     * @return MailPhrase 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
}


