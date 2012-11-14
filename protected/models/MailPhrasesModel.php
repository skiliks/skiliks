<?php



/**
 * Содержит набор фраз, которые соответствуют конкретному персонажу
 * Также есть связь по коду конструктора писем. Поле code например B1 W1
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailPhrasesModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailPhrasesModel 
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
    
    
    
    public function byIds($ids)
    {
        if (count($ids) == 0) return $this;
        
        $ids = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }
    
    public function byCharacterThemes($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "character_theme_id = {$id}"
        ));
        return $this;
    }
    
    public function byType($type)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "phrase_type = {$type}"
        ));
        return $this;
    }
    
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
}

?>
