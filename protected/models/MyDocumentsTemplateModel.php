<?php



/**
 * Шаблон документов. Потом нужные документы отсюда копируются в рамках симуляции 
 * в таблицу my_documents
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsTemplateModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MyDocumentsTemplateModel 
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
            return 'my_documents_template';
    }
    
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
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
