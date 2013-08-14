<?php

/**
 * This is the model class for table "log_account_invite".
 *
 * The followings are the available columns in table 'log_account_invite':
 * @property integer $id
 * @property integer $user_id
 * @property string $direction
 * @property integer $amount
 * @property string $action
 * @property integer $limit_after_transaction
 * @property string $comment
 * @property string $date
 */
class LogAccountInvite extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogAccountInvite the static model class
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
		return 'log_account_invite';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account_id, amount, limit_after_transaction', 'numerical', 'integerOnly'=>true),
			array('direction', 'length', 'max'=>10),
			array('comment, date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account_id, direction, amount, limit_after_transaction, comment, date', 'safe', 'on'=>'search'),
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
            'user' => [self::BELONGS_TO, 'YumUser', 'user_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'account_id' => 'Account',
			'direction' => 'Direction',
			'amount' => 'Amount',
			'limit_after_transaction' => 'Limit After Transaction',
			'comment' => 'Comment',
			'date' => 'Date',
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
		$criteria->compare('account_id',$this->account_id);
		$criteria->compare('direction',$this->direction,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('limit_after_transaction',$this->limit_after_transaction);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}