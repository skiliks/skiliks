<?php

/**
 * This is the model class for table "simulation_completed_parent".
 *
 * The followings are the available columns in table 'simulation_completed_parent':
 * @property integer $id
 * @property integer $sim_id
 * @property string $parent_code
 * @property string $end_time
 */
class SimulationCompletedParent extends CActiveRecord
{
    // Place your specific code there

    // ---------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SimulationCompletedParent the static model class
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
		return 'simulation_completed_parent';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('sim_id', 'required'),
			array('sim_id', 'numerical', 'integerOnly'=>true),
			array('parent_code', 'length', 'max'=>5),
			array('id, sim_id, parent_code', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'          => 'ID',
			'sim_id'      => 'Sim',
			'parent_code' => 'Parent Code',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('parent_code',$this->parent_code);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}