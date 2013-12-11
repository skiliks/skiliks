<?php
/**
 * Class MailPhrase
 *
 * @property integer $id
 * @property integer $character_theme_id, mail_character-themes.id
 * @property integer $phrase_type // ???
 * @property integer $column_number
 * @property string  $name
 * @property string  $import_id
 * @property string  $scenario_id
 * @property string  $constructor_id
 * @property string  $code, Constructor code, 'B1','R1' ...
 */
class MailPhrase extends CActiveRecord
{

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param string $className
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
}


