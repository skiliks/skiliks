<?php

/**
 * This is the model class for table "site_log_authorization".
 *
 * The followings are the available columns in table 'site_log_authorization':
 * @property integer $id
 * @property string $ip
 * @property integer $is_success
 * @property string $user_agent
 * @property string $date
 * @property string $login
 * @property string $password
 * @property string $referer_url
 * @property string $user_id
 * @property string $type_auth
 *
 * The followings are the available model relations:
 * @property YumUser $user
 */
class SiteLogAuthorization extends CActiveRecord
{
    const USER_AREA = 'user_area';
    const ADMIN_AREA = 'admin_area';

    const SUCCESS_AUTH = '1';
    const FAIL_AUTH = '0';

    /**
     * @return string
     */
    public function getStatus()
    {
        return ($this->is_success === self::SUCCESS_AUTH) ? 'Успешная' : 'Не успешная';
    }

    /* --------------------------------------------------------------------------------------------------- */

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return SiteLogAuthorization the static model class
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
        return 'site_log_authorization';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('is_success', 'numerical', 'integerOnly'=>true),
            array('ip', 'length', 'max'=>30),
            array('user_agent, login, password', 'length', 'max'=>255),
            array('user_id', 'length', 'max'=>10),
            array('type_auth', 'length', 'max'=>20),
            array('date, referer_url', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, ip, is_success, user_agent, date, login, password, referer_url, user_id, type_auth', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'YumUser', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'ip' => 'Ip',
            'is_success' => 'Is Success',
            'user_agent' => 'User Agent',
            'date' => 'Date',
            'login' => 'Login',
            'password' => 'Password',
            'referer_url' => 'Referral Url',
            'user_id' => 'User',
            'type_auth' => 'Type Auth',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('ip',$this->ip,true);
        $criteria->compare('is_success',$this->is_success);
        $criteria->compare('user_agent',$this->user_agent,true);
        $criteria->compare('date',$this->date,true);
        $criteria->compare('login',$this->login,true);
        $criteria->compare('password',$this->password,true);
        $criteria->compare('referer_url',$this->referer_url,true);
        $criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('type_auth',$this->type_auth,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @return CActiveDataProvider
     */
    public function searchSiteLogs() {

        $criteria = new CDbCriteria();
        $log = Yii::app()->request->getParam('SiteLogAuthorization');
        if(!empty($log['ip'])) {
            $criteria->addCondition('ip LIKE \'%'.$log['ip'].'%\'');
            $criteria->compare('ip', $log['ip'],true);
        }
        if(!empty($log['login'])) {
            $criteria->addCondition('login LIKE \'%'.$log['login'].'%\'');
            $criteria->compare('login', $log['login'],true);
        }


        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder'=>'id DESC',
                'sortVar' => 'sort',
                'attributes' => [
                    'id' => [
                        'asc'  => 'id',
                        'desc' => 'id DESC'
                    ]
                ],
            ],
            'pagination' => [
                'pageSize' => 15,
                'pageVar' => 'page'
            ]
        ]);
    }
}