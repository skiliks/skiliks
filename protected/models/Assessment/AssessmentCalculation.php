<?php
/**
 * This is the model class for table "assessment_calculation".
 *
 * The followings are the available columns in table 'assessment_calculation':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $point_id
 * @property float $value
 *
 * The followings are the available model relations:
 * @property Simulation $sim
 * @property HeroBehaviour $point
 */
class AssessmentCalculation extends CActiveRecord
{
    // Place your specific code there

    // ---------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssessmentCalculation the static model class
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
		return 'assessment_calculation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('sim_id, point_id', 'required'),
			array('sim_id, point_id', 'numerical', 'integerOnly'=>true),
			array('value', 'numerical'),
			array('id, sim_id, point_id, value', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'sim' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
			'point' => array(self::BELONGS_TO, 'HeroBehaviour', 'point_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'       => 'ID',
			'sim_id'   => 'Sim',
			'point_id' => 'Point',
			'value'    => 'Value'
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
		$criteria->compare('point_id',$this->point_id);
		$criteria->compare('value',$this->value);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}