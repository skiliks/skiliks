<?php

/**
 * This is the model class for table "user_account_personal".
 *
 * The followings are the available columns in table 'user_account_personal':
 * @property string $user_id
 * @property integer $industry_id
 * @property integer $position_id
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Industry $industry
 * @property Position $position
 */
class UserAccountPersonal extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserAccountPersonal the static model class
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
		return 'user_account_personal';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id'                 , 'required'),
			array('industry_id, position_id', 'numerical', 'integerOnly'=>true),
			array('user_id'                 , 'length'   , 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, industry_id, position_id', 'safe', 'on'=>'search'),
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
			'user'     => array(self::BELONGS_TO, 'User'    , 'user_id'),
			'industry' => array(self::BELONGS_TO, 'Industry', 'industry_id'),
			'position' => array(self::BELONGS_TO, 'Position', 'position_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id'     => Yii::t('site', 'User'),
			'industry_id' => Yii::t('site', 'Industry'),
			'position_id' => Yii::t('site', 'Position'),
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

		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('industry_id',$this->industry_id);
		$criteria->compare('position_id',$this->position_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}