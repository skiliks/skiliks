<?php



/**
 * Модель пользователей
 *
 * @property mixed email
 * @property mixed password
 * @property mixed is_active
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Users extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return Users
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
            return 'users';
    }
    
    public function byEmail($email)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "email = '{$email}'"
        ));
        return $this;
    }
    
    public function scopes()
    {
        return array(
            'active'=>array(
                'condition'=>'is_active=1',
            )
        );
    }
    
    public function isActive()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "is_active=1"
        ));
        return $this;
    }
    
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = '{$id}'"
        ));
        return $this;
    }
    
    public function byIds($ids)
    {
        $list = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$list})"
        ));
        return $this;
    }
}

?>
