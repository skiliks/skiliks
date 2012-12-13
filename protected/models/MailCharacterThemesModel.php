<?php

/**
 * Содержит соотношения - какому персонажу какой набор тем писем
 * соответствует
 * 
 * Связана с моделями: Characters, MailThemesModel.
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
    
    /**
     * Выбрать по заданному персонажу
     * @param int $characterId
     * @return MailCharacterThemesModel 
     */
    public function byCharacter($characterId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "character_id = {$characterId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданной теме
     * @param int $themeId
     * @return MailCharacterThemesModel 
     */
    public function byTheme($themeId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "theme_id = {$themeId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по идентификатору записи
     * @param int $id
     * @return MailCharacterThemesModel 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать с признаком "телефон"
     * @return MailCharacterThemesModel 
     */
    public function byPhone($v = 1)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "phone = {$v}"
        ));
        return $this;
    }
    
    /**
     * @return MailCharacterThemesModel 
     */
    public function byMail($v = 1)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail = {$v}"
        ));
        return $this;
    }
}


