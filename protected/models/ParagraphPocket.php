<?php

/**
 * This is the model class for table "paragraph_pocket".
 *
 * The followings are the available columns in table 'paragraph_pocket':
 * @property integer $id
 * @property integer $scenario_id
 * @property string $paragraph_alias
 * @property string $behaviour_alias
 * @property string $left_direction
 * @property double $left
 * @property string $right_direction
 * @property double $right
 * @property string $text
 *
 * The followings are the available model relations:
 * @property Scenario $scenario
 */
class ParagraphPocket extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ParagraphPocket the static model class
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
		return 'paragraph_pocket';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('scenario_id', 'required'),
			array('scenario_id', 'numerical', 'integerOnly'=>true),
			array('left, right', 'numerical'),
			array('paragraph_alias, behaviour_alias, left_direction, right_direction', 'length', 'max'=>255),
			array('text', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, scenario_id, paragraph_alias, behaviour_alias, left_direction, left, right_direction, right, text', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'scenario_id' => 'Scenario',
			'paragraph_alias' => 'Paragraph Alias',
			'behaviour_alias' => 'Behaviour Alias',
			'left_direction' => 'Left Direction',
			'left' => 'Left',
			'right_direction' => 'Right Direction',
			'right' => 'Right',
			'text' => 'Text',
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
		$criteria->compare('scenario_id',$this->scenario_id);
		$criteria->compare('paragraph_alias',$this->paragraph_alias,true);
		$criteria->compare('behaviour_alias',$this->behaviour_alias,true);
		$criteria->compare('left_direction',$this->left_direction,true);
		$criteria->compare('left',$this->left);
		$criteria->compare('right_direction',$this->right_direction,true);
		$criteria->compare('right',$this->right);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}