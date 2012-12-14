<?php
/**
 * !!!
 * Looks like we must use MailReceiversModel instaad of MailCopiesModel
 * 
 * Содержит набор персонажей, которые идут в копии к заданному письму
 * 
 * Связана с моделями: Characters, MailBoxModel.
 * 
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailCopiesModel extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * mail_box.id
     * @var integer
     */
    public $mail_id;
    
    /**
     * characters.id
     * @var int
     */
    public $receiver_id;     
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
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
    
    /**
     * Выбрать по заданному письму
     * @param int $mailId
     * @return MailCopiesModel 
     */
    public function byMailId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
}


