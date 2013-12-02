<?php

/**
 * table "free_email_provider".
 *
 * Just list of domains to check is user email corporate or free
 *
 * Based on:
 * @link: http://freecentral2.tripod.com/freemail.htm
 * @link:http://www.joewein.net/spam/spam-freemailer.htm
 *
 * @property integer $id
 * @property string $domain
 * @property string $security_risk
 */
class FreeEmailProvider extends CActiveRecord
{
    const FREE_MAIL = 'free_mail';

    const TEN_MINUTES_MAIL = 'ten_minutes_mail';
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FreeEmailProvider the static model class
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
		return 'free_email_provider';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('domain', 'length', 'max'=>100),
            array('domain', 'validateDomainName'),
            array('domain', 'validateDomainExists'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'     => 'ID',
			'domain' => 'Домен',
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
		$criteria->compare('domain',$this->domain);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function validateDomainName($attribute,$params) {
        if(!preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/', $this->$attribute)){
            $this->addError($attribute, 'Невалидный домен');
        }
    }

    public function validateDomainExists($attribute,$params) {
        if($this->findByAttributes(['domain'=>$this->$attribute]) !== null){
            $this->addError($attribute, 'Такой домен уже добавлен');
        }
    }

    public function searchEmails() {

        $criteria = new CDbCriteria();
        $email = Yii::app()->request->getParam('FreeEmailProvider');
        if(!empty($email['domain'])) {
           $criteria->addCondition('domain LIKE \'%'.$email['domain'].'%\'');
           $criteria->compare('domain', $email['domain'],true);
        }


        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder'=>'id DESC',
                'sortVar' => 'sort',
                'attributes' => [
                    'domain' => [
                        'asc'  => 'domain',
                        'desc' => 'domain DESC'
                    ],
                    'id' => [
                        'asc'  => 'id',
                        'desc' => 'id DESC'
                    ]
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'pageVar' => 'page'
            ]
        ]);
    }

    public function getActions() {
        if($this->security_risk === self::TEN_MINUTES_MAIL){
            $alias = self::FREE_MAIL;
        }else{
            $alias = self::TEN_MINUTES_MAIL;
        }
        return CHtml::link($alias, '/admin_area/change_security_risk?set='.$alias.'&id='.$this->id);
    }
}