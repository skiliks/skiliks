<?php


/**
 * Модель кодов активации для конкретного пользователя
 *
 * Связана с моделями: Users.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UsersActivationCode extends CActiveRecord
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
     * @var string
     */
    public $code;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'users_activation_code';
    }
    
    /**
     * Выбрать по коду
     * @param string $code
     * @return UsersActivationCode 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
}


