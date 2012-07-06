<?php



/**
 * Модель пользователей
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Users extends CActiveRecord{
    
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
}

?>
