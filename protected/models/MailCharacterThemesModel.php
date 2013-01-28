<?php

/**
 * Содержит соотношения - какому персонажу какой набор тем писем
 * соответствует
 * 
 * Связана с моделями: Characters
 *
 * @property MailTemplateModel letter
 * @property string constructor_number
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailCharacterThemesModel extends CActiveRecord
{
     /**
     * @var integer
     */
    public $id;
    
    /**
     * @var integer
     */
    public $character_id;
    
    /**
     * @var string
     */
    public $text;
    
    /**
     * @var string
     */
    public $letter_number; // "code" - "M2", "MS45" ...
    
    /**
     * @var string
     */
    public $wr; // right, werong : "R", "W"
    
    /**
     * @var string
     */
    public $construst_number; // "R1", "TXT" ...
    
    /**
     * @var string
     */
    public $phone; // "R1", "TXT" ...
    
    /**
     * @var string
     */
    public $phone_wr; // right, werong : "R", "W"
    
    /**
     * @var string
     */
    public $phone_dialog_number; // ??
    
    /**
     * @var int
     */
    public $mail; // ??
    
    /**
     * @var string
     * 
     * "manual" - user write new letter and send it,
     * "dialog" - new mail window was opened by dialog
     * "inbox"  - user write reply email
     */
    public $source;   
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @param string $receivers, '1,2,3'
     * @param integer $mailThemeId
     * 
     * @return integer || NULL
     */
    public static function getCharacterThemeId($receivers, $mailThemeId)
    {
        $characterThemeId = NULL;
        $receiversArr = explode(',', $receivers);

        if (0 < count($receiversArr) && NULL != $mailThemeId) {
            $characterTheme = MailCharacterThemesModel::model()
                ->byCharacter(reset($receiversArr))
                ->byTheme($mailThemeId)
                ->find();

            if (null !== $characterTheme) {
                $characterThemeId = $characterTheme->id;
            }
        }
        
        return $characterThemeId;
    }


    /** ------------------------------------------------------------------------------------------------------------ **/
    
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
            'condition' => "character_id = :characterId",
            'params' => ['characterId' => $characterId]
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
     * Выборка по набору тем
     * @param array $ids
     * @return MailCharacterThemesModel
     */
    public function byIds($ids)
    {
        $ids = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }
    
    /**
     * @param string $ids
     * @return \MailCharacterThemesModel
     */
    public function byIdsNotIn($ids)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " `id` NOT IN ({$ids}) "
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
     * Выбрать по заданной теме
     * @param int $themeId
     * @return MailCharacterThemesModel
     */
    public function byTheme($themeId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$themeId}"
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
    
    /**
     * @param string $code, mail code 'M1', 'MS2' etc.
     * @return MailCharacterThemesModel 
     */
    public function byLetterNumber($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "letter_number = '{$code}'"
        ));
        return $this;
    }
    
    /**
     * @param string $text, like 'Служебная записка о сервере. Срочно!' e.g.
     * @return MailCharacterThemesModel 
     */
    public function byText($text)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "text = '{$text}'"
        ));
        return $this;
    }

    public function relations()
    {
        return array(
            'letter' => array(self::BELONGS_TO, 'MailTemplateModel', 'letter_number')
        );
    }
}


