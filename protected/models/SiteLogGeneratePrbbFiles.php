<?php

/**
 * This is the model class for table "site_log_generate_prbb_files".
 *
 * The followings are the available columns in table 'site_log_generate_prbb_files':
 * @property integer $id
 * @property string $started_at
 * @property string $finished_at
 * @property string $started_by_id
 * @property string $path
 * @property string $result
 *
 * The followings are the available model relations:
 * @property YumUser $startedBy
 */
class SiteLogGeneratePrbbFiles extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SiteLogGeneratePrbbFiles the static model class
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
		return 'site_log_generate_prbb_files';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('started_by_id', 'length', 'max'=>10),
			array('started_at, finished_at, result, path', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, started_at, finished_at, started_by_id, result, path', 'safe', 'on'=>'search'),
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
			'startedBy' => array(self::BELONGS_TO, 'YumUser', 'started_by_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'started_at' => 'Started At',
			'finished_at' => 'Finished At',
			'started_by_id' => 'Started By',
			'path' => 'Path',
			'result' => 'Result',
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
		$criteria->compare('started_at',$this->started_at,true);
		$criteria->compare('finished_at',$this->finished_at,true);
		$criteria->compare('started_by_id',$this->started_by_id,true);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('result',$this->result,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}