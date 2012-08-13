<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailBoxModel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailBoxModel 
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
            return 'mail_box';
    }
    
    
    public function byFolder($folderId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "group_id = {$folderId}"
        ));
        return $this;
    }
    
    public function byReceiver($receiverId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "receiver_id = {$receiverId}"
        ));
        return $this;
    }
    
    public function bySender($senderId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sender_id = {$senderId}"
        ));
        return $this;
    }
    
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    public function orderBy($fieldName, $direction)
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "$fieldName $direction"
        ));
        return $this;
    }
}

?>
