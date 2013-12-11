<?php

/**
 * This is the model class for table "site_log_account_action".
 *
 * The followings are the available columns in table 'site_log_account_action':
 * @property integer $id
 * @property string $user_id
 * @property string $ip
 * @property string $message
 * @property string $date
 *
 * The followings are the available model relations:
 * @property YumUser $user
 */
class SiteLogAccountAction extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return SiteLogAccountAction the static model class
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
        return 'site_log_account_action';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id', 'length', 'max'=>10),
            array('ip', 'length', 'max'=>40),
            array('message', 'length', 'max'=>255),
            array('date', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, ip, message, date', 'safe', 'on'=>'search'),
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
            'user_id' => 'User',
            'ip' => 'Ip',
            'message' => 'Message',
            'date' => 'Date',
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
        $criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('ip',$this->ip,true);
        $criteria->compare('message',$this->message,true);
        $criteria->compare('date',$this->date,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @param integer $user_id
     * @return CActiveDataProvider
     */
    public function searchSiteLogs($user_id) {

        $criteria = new CDbCriteria();
        $criteria->addCondition('user_id = '.$user_id);
        $criteria->compare('user_id', $user_id,true);
        $log = Yii::app()->request->getParam('SiteLogAccountAction');
        if(!empty($log['ip'])) {
            $criteria->addCondition('ip LIKE \'%'.$log['ip'].'%\'');
            $criteria->compare('ip', $log['ip'],true);
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