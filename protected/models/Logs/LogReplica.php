<?php

/**
 * This is the model class for table "log_replica".
 *
 * The followings are the available columns in table 'log_replica':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $replica_id
 * @property string $time
 *
 * The followings are the available model relations:
 * @property Replica $replica
 * @property Simulation $sim
 */
class LogReplica extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogReplica the static model class
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
		return 'log_replica';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, replica_id, time', 'required'),
			array('sim_id, replica_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, replica_id, time', 'safe', 'on'=>'search'),
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
			'replica' => array(self::BELONGS_TO, 'Replica', 'replica_id'),
			'sim' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sim_id' => 'Sim',
			'replica_id' => 'Replica',
			'time' => 'Time',
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
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('replica_id',$this->replica_id);
		$criteria->compare('time',$this->time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}