<?php

/**
 *
 */
class ActivityEfficiencyCondition extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $activity_id;
    
    /**
     * @var string
     */
    public $type;
    
    /**
     * dialog.excel_id || mail_template.code || my_document_template.code
     * @var string
     */
    public $result_code;
    
    /**
     * mail_template.mysql_id
     * @var integer
     */
    public $email_template_id;
    
    /**
     * dialog.mysql_id
     * @var integer
     */
    public $dialog_id;
    
    /**
     * @var string
     */
    public $operation;    
    
    /**
     * @var integer
     */
    public $efficiency_value;
    
    /**
     * @var string
     */
    public $fail_less_coeficient;
    
    /**
     * Systen value to check is entity just imported or old after reimport and delete olds.
     * @var string
     */
    public $import_id;
    
    /* ------------------------------------------------------------------------------------------------------------- **/

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Activity the static model class
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
		return 'activity_efficiency_conditions';
	}
    
    public function byActivityId($activity_id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "activity_id = '{$activity_id}'"
        ));
        return $this;
    }
    
    public function byResultCode($result_code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "result_code = '{$result_code}'"
        ));
        return $this;
    }
    
    public function byType($type)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "type = '{$type}'"
        ));
        return $this;
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	/*public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent, grandparent, name', 'required'),
			array('id, parent, grandparent', 'length', 'max'=>10),
            array('name', 'length', 'max'=>255),
            array('import_id', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parent, grandparent, name, category_id, import_id', 'safe', 'on'=>'search'),
		);
	}*/

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('activity_id',$this->activity_id,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('result_code',$this->result_code,true);
		$criteria->compare('email_template_id',$this->email_template_id,true);
		$criteria->compare('dialog_id',$this->dialog_id,true);
		$criteria->compare('operation',$this->operation,true);
		$criteria->compare('efficiency_value',$this->efficiency_value,true);
		$criteria->compare('fail_less_coeficient',$this->fail_less_coeficient,true);
		$criteria->compare('import_id',$this->import_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}