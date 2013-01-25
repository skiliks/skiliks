<?php



/**
 * Модель групп польщзователя
 *
 * Связана с моделями:  Groups, Users.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserGroupsModel extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * user.id
     * @var int
     */
    public $uid;
    
    /**
     * user_groups.id
     * @var int
     */
    public $gid;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return UserGroupsModel
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
            return 'user_groups';
    }
    
    /**
     * Выбрать по заданному пользователю
     * @param int $uid
     * @return UserGroupsModel 
     */
    public function byUser($uid)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "uid = {$uid}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданной группе
     * @param int $gid
     * @return UserGroupsModel 
     */
    public function byGroup($gid)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "gid = {$gid}"
        ));
        return $this;
    }
}


