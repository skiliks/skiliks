<?php
/**
 * Шаблон персонажей, которые идут копией к конкретному письму.
 *
 * @property integer $id
 * @property integer $mail_id, mail_template.id
 * @property integer $receiver_id, characters.id
 */
class MailTemplateCopy extends CActiveRecord
{
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @param string $className
     * @return MailTemplateCopy
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
        return 'mail_copies_template';
    }
}