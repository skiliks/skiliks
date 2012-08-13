<?php



/**
 * Description of MailCopiesModel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailCopiesModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailCopiesModel 
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
            return 'mail_copies';
    }
    
    public function byMailId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
}

?>
