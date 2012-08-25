<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailAttachmentsModel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailAttachmentsModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailAttachmentsModel 
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
            return 'mail_attachments';
    }
    
    
    public function byMailId($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
    
    public function byMailIds($mailIds)
    {
        $ids = implode(',', $mailIds);
        
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id in ({$ids})"
        ));
        return $this;
    }
}

?>
