<?php



/**
 * Description of UserGroupsModel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserGroupsModel extends CActiveRecord{
    
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
    
    public function byUser($uid)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "uid = '{$uid}'"
        ));
        return $this;
    }
}

?>
