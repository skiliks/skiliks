<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailPhrases
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailPhrasesModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailPhrasesModel 
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
    
    
    
    public function byIds($ids)
    {
        if (count($ids) == 0) return $this;
        
        $ids = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }
}

?>
