<?php



/**
 * Модель задач
 * 
 * Связана с моделями:  Simulations
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Tasks extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $title;
    
    /**
     * game minutes
     * @var integer
     */
    public $start;
    
    /**
     * game minutes
     * @var integer
     */
    public $duration;
    
    /**
     * @var integer
     */
    public $type;    
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;
    
    /**
     * Code, '','' ...
     * @var string
     */
    public $code;  
    
    /**
     * 'start', 'new', null
     * @var string
     */
    public $start_type;
    
    /**
     * @var integer
     */
    public $category; // ?  
    
    /** ------------------------------------------------------------------------------------------------------------ **/

    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return Tasks 
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
            return 'tasks';
    }
    
    /**
     * Выбрать согласно набору задач
     * @param array $ids
     * @return Tasks 
     */
    public function byTitles($titles)
    {
        $titles = implode("','", $titles);
        
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "title in ('{$titles}')"
        ));
        return $this;
    }
    
    /**
     * Выбрать согласно набору задач
     * @param array $ids
     * @return Tasks 
     */
    public function byIds($ids)
    {
        
        $ids = implode(',', $ids);
        
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }
    
    /**
     * Выбрать конкретную задачу
     * @param int $id
     * @return Tasks 
     */
    public function bySimId($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать конкретную задачу
     * @param int $id
     * @return Tasks 
     */
    public function bySimIdOrNull($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " (sim_id = {$simId} OR sim_id IS NULL) "
        ));
        return $this;
    }
    
    /**
     * Выбрать конкретную задачу
     * @param int $id
     * @return Tasks 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по коду задачи
     * @param int $code
     * @return Tasks 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
    
    
    
    public function byStartType($startType)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "start_type = '{$startType}'"
        ));
        return $this;
    }
}


