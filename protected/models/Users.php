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

    public function relations()
    {
        return array(
            'simulations' => array(self::HAS_MANY, 'Simulations', 'user_id', 'order' => 'id DESC')
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'users';
    }
    
    /**
     * Выбрать по емейлу
     * @param string $email
     * @return Users 
     */
    public function byEmail($email)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "email = '{$email}'"
        ));
        return $this;
    }
    
    // не используется
    public function scopes()
    {
        return array(
            'active'=>array(
                'condition'=>'is_active=1',
            )
        );
    }
    
    /**
     * Выбрать активных пользователей
     * @return Users 
     */
    public function isActive()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "is_active=1"
        ));
        return $this;
    }
    
    /**
     * Выбрать конкретного пользователя
     * @param int $id
     * @return Users 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = '{$id}'"
        ));
        return $this;
    }
    
    /**
     * Выбрать набор пользователей
     * @param array $ids
     * @return Users 
     */
    public function byIds($ids)
    {
        $list = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$list})"
        ));
        return $this;
    }
    
    public function encryptPassword($password)
    {
        return md5($password);
    }
}


