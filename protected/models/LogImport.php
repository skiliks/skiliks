<?php

/**
 * This is the model class for table "log_import".
 *
 * The followings are the available columns in table 'log_import':
 * @property integer $id
 * @property string $user_id
 * @property integer $scenario_id
 * @property string $started_at
 * @property string $finished_at
 * @property string $text
 *
 * The followings are the available model relations:
 * @property Scenario $scenario
 * @property YumUser $user
 */
class LogImport extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogImport the static model class
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
		return 'log_import';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('scenario_id', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>10),
			array('started_at, finished_at, test', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, scenario_id, started_at, finished_at, test', 'safe', 'on'=>'search'),
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
			'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
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
			'scenario_id' => 'Scenario',
			'started_at' => 'Started At',
			'finished_at' => 'Finished At',
			'test' => 'Test',
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
		$criteria->compare('scenario_id',$this->scenario_id);
		$criteria->compare('started_at',$this->started_at,true);
		$criteria->compare('finished_at',$this->finished_at,true);
		$criteria->compare('test',$this->test,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}