<?php


/**
 * Модель логирования открытых окон в рамках симуляции.
 * 
 * Связана с моделями: Simulations.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class WindowLogModel extends CActiveRecord{

    /**
     *
     * @param type $className
     * @return WindowLogModel
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
            return 'window_log';
    }
    
    /**
     * Выбрать конкретную запись в логах
     * @param int $fileId
     * @return WindowLogModel 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать в рамках заданной симуляции
     * @param int $simId
     * @return WindowLogModel 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по активному окну
     * @param int $activeWindow
     * @return WindowLogModel 
     */
    public function byActiveWindow($activeWindow)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "activeWindow = {$activeWindow}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по активному подокну
     * @param int $subWindow
     * @return WindowLogModel 
     */
    public function byActiveSubWindow($subWindow)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "activeSubWindow = {$subWindow}"
        ));
        return $this;
    }
    
    /**
     * Выбрать где активное подокно не равно заданному
     * @param int $activeWindow
     * @return WindowLogModel 
     */
    public function notActiveWindow($activeWindow)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "activeWindow <> {$activeWindow}"
        ));
        return $this;
    }
    
    /**
     * Окно еще не закрыто
     * @return WindowLogModel 
     */
    public function isNotClosed()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "timeEnd = 0 or timeEnd is null"
        ));
        return $this;
    }
    
    /**
     * Выбрать ближайшую запись
     * @return WindowLogModel 
     */
    public function nearest()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "id desc",       
            'limit' => 1,
        ));
        return $this;
    }
}

?>
