<?php

/**
 * This is the model class for table "learning_area".
 *
 * The followings are the available columns in table 'learning_area':
 * @property integer $id
 * @property string $code
 * @property string $title
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property LearningGoal[] $learningGoals
 */
class LearningArea extends CActiveRecord
{
    // Place your specific code there

    // ---------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LearningArea the static model class
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
		return 'learning_area';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('code', 'required'),
			array('code', 'length', 'max'=>10),
			array('import_id', 'length', 'max'=>14),
			array('title', 'safe'),
			array('code, title, import_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'learningGoals' => array(self::HAS_MANY, 'LearningGoal', 'learning_area_code'),
            'learningGoalGroups' => array(self::HAS_MANY, 'LearningGoalGroup', 'learning_area_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'code'      => 'Code',
			'title'     => 'Title',
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
		$criteria->compare('code',$this->code);
		$criteria->compare('title',$this->title);
		$criteria->compare('import_id',$this->import_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}