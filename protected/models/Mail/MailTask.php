<?php
/**
 * Набор задач для писем.
 *
 * @property integer $id
 * @property integer $mail_id, mail_box.id
 * @property string $name
 * @property integer $duration, in game minutes
 * @property string $code, 'M1', 'M8'
 * @property string $wr, right, wrong : "R", "W"
 * @property integer $category // ?
 * @property string $import_id
 */
class MailTask extends CActiveRecord
{
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @param string $className
     * @return MailTask
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
        return 'mail_tasks';
    }
}


