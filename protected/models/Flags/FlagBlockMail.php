<?php
/**
 * The followings are the available columns in table 'flag_block_mail':
 * @property integer $id
 * @property string $flag_code
 * @property integer $value
 * @property integer $mail_template_id
 *
 * The followings are the available model relations:
 * @property Flag $flagCode
 * @property MailTemplate $mailTemplate
 */
class FlagBlockMail extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FlagBlockMail the static model class
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
		return 'flag_block_mail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('flag_code, mail_template_id', 'required'),
			array('value, mail_template_id', 'numerical', 'integerOnly'=>true),
			array('flag_code', 'length', 'max'=>5),
			array('id, flag_code, value, mail_template_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'flagCode' => array(self::BELONGS_TO, 'Flag', 'flag_code'),
			'mailTemplate' => array(self::BELONGS_TO, 'MailTemplate', 'mail_template_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'flag_code' => 'Flag Code',
			'value' => 'Value',
			'mail_template_id' => 'Mail Template',
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
		$criteria->compare('flag_code',$this->flag_code);
		$criteria->compare('value',$this->value);
		$criteria->compare('mail_template_id',$this->mail_template_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}