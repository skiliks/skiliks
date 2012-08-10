<?php

/**
 * Description of MailCharacterThemesModel
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
}

?>
