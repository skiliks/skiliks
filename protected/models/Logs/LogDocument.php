<?php

/**
 * This is the model class for table "log_documents".
 *
 * The followings are the available columns in table 'log_documents':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $file_id
 * @property string $start_time
 * @property string $end_time
 * @property MyDocument file
 */
class LogDocument extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogDocument the static model class
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
		return 'log_documents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, file_id, start_time', 'required'),
			array('sim_id, file_id', 'numerical', 'integerOnly'=>true),
			array('end_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, file_id, start_time, end_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
            'file' => [self::BELONGS_TO, 'MyDocument', 'file_id'],
            'simulation' => [self::BELONGS_TO, 'Simulation', 'sim_id']
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sim_id' => 'Sim',
			'file_id' => 'File',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
		);
	}

    protected function afterSave()
    {
        if(false === Yii::app()->params['disableOldLogging']) {

            $activityAction = ActivityAction::model()->findByPriority(
                ['document_id' => $this->file->template_id],
                null,
                $this->simulation
            );

            if ($activityAction !== null) {
                $activityAction->appendLog($this);
            } else {
                //throw new CException("The document must have id"); TODO:Костыль, для лайт это 500-ая, нужно исправить в релизе 2
            }
        }
        parent::afterSave();
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
		$criteria->compare('file_id',$this->file_id);
		$criteria->compare('start_time',$this->start_time);
		$criteria->compare('end_time',$this->end_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}