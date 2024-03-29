<?php

/**
 * This is the model class for table "activity_parent".
 *
 * The followings are the available columns in table 'activity_parent':
 * @property integer $id
 * @property string $import_id
 * @property string $parent_code
 * @property integer $dialog_id
 * @property integer $mail_id
 */
class ActivityParent extends CActiveRecord
{
    /**
     * Returns true if parent is already terminated in simulation
     * @param $simulation Simulation
     * @return bool
     */
    public function isTerminatedInSimulation($simulation)
    {
        return SimulationCompletedParent::model()->countByAttributes([
            'sim_id' => $simulation->primaryKey, 'parent_code' => $this->parent_code
        ]);
    }

    /**
     * Terminates parent activity in given simulation
     * @param $simulation Simulation
     */
    public function terminateInSimulation($simulation, $end_time)
    {
        $simulationCompletedParent = new SimulationCompletedParent();
        $simulationCompletedParent->sim_id = $simulation->primaryKey;
        $simulationCompletedParent->parent_code = $this->parent_code;
        $simulationCompletedParent->end_time = $end_time;
        $simulationCompletedParent->save();
    }

    // ---------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ActivityParent the static model class
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
		return 'activity_parent';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('parent_code', 'required'),
			array('id, dialog_id, mail_id', 'numerical', 'integerOnly'=>true),
			array('import_id', 'length', 'max'=>14),
			array('parent_code', 'length', 'max'=>10),
			array('id, import_id, parent_code, dialog_id, mail_id', 'safe', 'on'=>'search'),
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
			'import_id'   => 'Import',
			'parent_code' => 'Parent Code',
			'dialog_id'   => 'Replica',
			'mail_id'     => 'Mail',
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
		$criteria->compare('import_id',$this->import_id);
		$criteria->compare('parent_code',$this->parent_code);
		$criteria->compare('dialog_id',$this->dialog_id);
		$criteria->compare('mail_id',$this->mail_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}