<?php

/**
 * This is the model class for table "site_log_permission_changes".
 *
 * The followings are the available columns in table 'site_log_permission_changes':
 * @property integer $id
 * @property string $created_at
 * @property string $initiator_id
 * @property string $result
 *
 * The followings are the available model relations:
 * @property YumUser $Initiator
 */
class SiteLogPermissionChanges extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SiteLogPermissionChanges the static model class
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
		return 'site_log_permission_changes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('initiator_id', 'length', 'max'=>10),
			array('created_at, result', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, created_at, initiator_id, result', 'safe', 'on'=>'search'),
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
			'Initiator' => array(self::BELONGS_TO, 'YumUser', 'initiator_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created_at' => 'Created At',
			'initiator_id' => 'Initiator',
			'result' => 'Result',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
     * @property string $order
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($order = ' t.id ASC ')
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('initiator_id',$this->initiator_id,true);
		$criteria->compare('result',$this->result,true);
        $criteria->order = $order;

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}