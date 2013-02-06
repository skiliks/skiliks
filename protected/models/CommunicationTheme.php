<?php

/**
 * Содержит соотношения - какому персонажу какой набор тем писем
 * соответствует
 * 
 * Связана с моделями: Characters
 *
 * @property MailTemplateModel letter
 * @property string constructor_number
 * @property string import_id
 * @property mixed|null mail_prefix
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CommunicationTheme extends CActiveRecord
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
    
    public $mail_prefix;


    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     * @param string $receivers, '1,2,3'
     * @param $parentSubjectId
     *
     * @return integer || NULL
     */
    public static function getCharacterThemeId($receivers, $parentSubjectId)
    {
        $characterThemeId = NULL;
        $receiversArr = explode(',', $receivers);

        if (0 < count($receiversArr) && NULL != $parentSubjectId) {
            $characterTheme = CommunicationTheme::model()->findByAttributes([
                'character_id' => reset($receiversArr),
                'code' => CommunicationTheme::model()->findByPk($parentSubjectId)->code
            ]);

            if (null !== $characterTheme) {
                $characterThemeId = $characterTheme->id;
            }
        }
        
        return $characterThemeId;
    }

    /**
     * @return string
     */
    public function getFormattedThemePrefix()
    {
        return str_replace(['re', 'fwd'], ['Re: ', 'Fwd: '], $this->mail_prefix) . '' . $this->text;
    }

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return CommunicationTheme
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
            return 'communication_themes';
    }
    
    /**
     * Выбрать по заданному персонажу
     * @param int $characterId
     * @return CommunicationTheme
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
     * @return CommunicationTheme
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
     * @return CommunicationTheme
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
     * @return CommunicationTheme
     */
    public function byIdsNotIn($ids)
    {
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition('id', explode(',',$ids));
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }

    /**
     * Выбрать с признаком "телефон"
     * @param int $v
     * @return CommunicationTheme
     */
    public function byPhone($v = 1)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('phone', $v);
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }

    /**
     * Выбрать по заданной теме
     * @param int $themeId
     * @return CommunicationTheme
     */
    public function byTheme($themeId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$themeId}"
        ));
        return $this;
    }

    /**
     * @param int $v
     * @return CommunicationTheme
     */
    public function byMail($v = 1)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('mail', $v);
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }
    
    /**
     * @param string $code, mail code 'M1', 'MS2' etc.
     * @return CommunicationTheme
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
     * @return CommunicationTheme
     */
    public function byText($text)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "text = '{$text}'"
        ));
        return $this;
    }
    
    /**
     * @return MailTemplateModel | NULL
     */
    public function getMailTemplate() {
        return MailTemplateModel::model()->find([
            'condition' => 'code = :code',
            'params'    => [
                'code' => $this->letter_number
            ]
        ]);
    }

    public function relations()
    {
        return [];
        /*return array(
            'letter' => array(self::BELONGS_TO, 'MailTemplateModel', 'letter_number')
        );*/
    }
}


