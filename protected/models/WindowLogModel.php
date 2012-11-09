<?php


/**
 * Description of WindowLogModel
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
    
    
    public function byId($fileId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$fileId}"
        ));
        return $this;
    }
    
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    public function byActiveWindow($activeWindow)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "activeWindow = {$activeWindow}"
        ));
        return $this;
    }
    
    public function byActiveSubWindow($subWindow)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "activeSubWindow = {$subWindow}"
        ));
        return $this;
    }
    
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
