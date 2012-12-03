<?php



/**
 * Модель симуляции.
 * 
 * Связана с моделями: Users.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Simulations extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return Simulations 
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
            return 'simulations';
    }
    
    /**
     * Выбрать по заданному пользователю.
     * @param int $uid
     * @return Simulations 
     */
    public function byUid($uid)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'user_id = '.(int)$uid
        ));
        return $this;
    }
    
    /**
     * Выбрать ближайшую симуляцию
     * @return Simulations 
     */
    public function nearest()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => 'id DESC',
            'limit' => 1
        ));
        return $this;
    }
    
    /**
     * Выбрать по идентификатору
     * @param int $id
     * @return Simulations 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'id = '.(int)$id
        ));
        return $this;
    }
}


