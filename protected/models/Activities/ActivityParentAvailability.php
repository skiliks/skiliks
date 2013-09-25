<?php

/**
 * This is the model class for table "activity_parent_availability".
 *
 * The followings are the available columns in table 'activity_parent_availability':
 * @property integer $id
 * @property integer $scenario_id
 * @property string $code
 * @property string $category
 * @property string $available_at
 * @property string $import_id
 * @property integer $is_keep_last_category
 * @property integer $must_present_for_214d
 */
class ActivityParentAvailability extends CActiveRecord
{
    const MUST_PRESENT_FOR_214D_YES = '1';
    const MUST_PRESENT_FOR_214D_NO = '0';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ActivityParentAvailability the static model class
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
		return 'activity_parent_availability';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, category, available_at', 'required'),
			array('code, category', 'length', 'max'=>10),
			array('import_id', 'length', 'max'=>14),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, code, category, available_at, import_id', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'code' => 'Code',
			'category' => 'Category',
			'available_at' => 'Available At',
			'import_id' => 'Import',
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
		$criteria->compare('code',$this->code,true);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('available_at',$this->available_at,true);
		$criteria->compare('import_id',$this->import_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}