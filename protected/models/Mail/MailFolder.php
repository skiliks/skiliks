<?php
/**
 * Class MailFolder
 *
 * @property integer $id
 * @property string $name
 */

class MailFolder extends CActiveRecord
{
    const INBOX_ID  = 1;
    const SENDED_ID = 3;
    
    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     * @param string $className
     * @return CActiveRecord
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
        return 'mail_group';
    }

}


