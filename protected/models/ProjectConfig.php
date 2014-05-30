<?php

/**
 * This is the model class for table "project_config".
 *
 * The followings are the available columns in table 'project_config':
 * @property integer $id
 * @property string $alias
 * @property string $type
 * @property string $value
 * @property bool $is_use_in_simulation
 * @property string $description
 */
class ProjectConfig extends CActiveRecord
{
    public static $type = [
        'String' => 'String',
        'Boolean' => 'Boolean',
        'Float' => 'Float',
    ];

    public static $is_use_in_sim = [
        0 => 'Нет',
        1 => 'Да'
    ];

    /**
     * @return mixed (bool|float|string)
     *
     * @throws Exception
     */
    public function getValue() {
        $methodName = 'get' . ucfirst($this->type);

        if (false == method_exists($this, $methodName)) {
            throw new Exception(
                sprintf(
                    'Try to get config %s of unknown type %s.',
                    $this->alias,
                    $this->type
                )
            );
        }

        return $this->{$methodName}();
    }

    /**
     * @return bool
     */
    public function getBoolean() {
        return (Boolean)$this->value;
    }

    /**
     * @return float
     */
    public function getFloat() {
        return (Float)$this->value;
    }

    /**
     * @return string
     */
    public function getString() {
        return (String)$this->value;
    }

    public function getOldAttributes() {
        return $this->_oldattributes;
    }

    // --------------------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectConfig the static model class
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
		return 'project_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('alias', 'length', 'max'=>120),
			array('type, value', 'length', 'max'=>30),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, alias, type, value, description, is_use_in_simulation', 'safe', 'on'=>'search'),
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
			'alias' => 'Alias',
			'type' => 'Type',
			'value' => 'Value',
			'description' => 'Description',
			'is_use_in_simulation' => 'Is use in simulation',
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
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('is_use_in_simulation',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}