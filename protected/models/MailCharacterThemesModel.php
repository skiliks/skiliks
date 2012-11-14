<?php

/**
 * Содержит соотношения - какому персонажу какой набор тем писем
 * соответствует
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailCharacterThemesModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailCharacterThemesModel 
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
            return 'mail_character_themes';
    }
    
    public function byCharacter($characterId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "character_id = {$characterId}"
        ));
        return $this;
    }
    
    public function byTheme($themeId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "theme_id = {$themeId}"
        ));
        return $this;
    }
    
    
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    public function byPhone()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "phone = 1"
        ));
        return $this;
    }
}

?>
