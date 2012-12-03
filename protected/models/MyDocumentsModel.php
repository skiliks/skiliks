<?php


/**
 * Модель моих документов
 *
 * Связана с моделями:  Simulations, MyDocumentsTemplateModel.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MyDocumentsModel 
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
            return 'my_documents';
    }
    
    /**
     * Выбрать по заданному идентификатору симуляции
     * @param int $simId
     * @return MyDocumentsModel 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать заданный документ
     * @param int $id
     * @return MyDocumentsModel 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному имени файла
     * @param string $fileName
     * @return MyDocumentsModel 
     */
    public function byFileName($fileName)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "fileName = '{$fileName}'"
        ));
        return $this;
    }
    
    /**
     * Отсортировать по имени файла
     * @return MyDocumentsModel 
     */
    public function orderByFileName()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "fileName asc"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному шаблону документа
     * @param int $templateId
     * @return MyDocumentsModel 
     */
    public function byTemplateId($templateId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "template_id = {$templateId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать только видимые документы 
     * @return MyDocumentsModel 
     */
    public function visible()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "hidden = 0"
        ));
        return $this;
    }
}


