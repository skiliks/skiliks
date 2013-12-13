<?php

/**
 * This is the model class for table "assessment_group".
 *
 * The followings are the available columns in table 'assessment_group':
 * @property integer $id
 * @property string $name
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property HeroBehaviour[] $heroBehaviours
 */
class AssessmentGroup extends CActiveRecord
{
    // Place your specific code there

    // ---------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssessmentGroup the static model class
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
		return 'assessment_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'length', 'max'=>255),
			array('import_id', 'length', 'max'=>14),
			array('id, name, import_id', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'name' => 'Name',
			'import_id' => 'Import',
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
		$criteria->compare('name',$this->name);
		$criteria->compare('import_id',$this->import_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}