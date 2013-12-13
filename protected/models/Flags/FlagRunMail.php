<?php
/**
 * The followings are the available columns in table 'flag_run_email':
 * @property string $flag_code
 * @property string $mail_code
 */
class FlagRunMail extends CActiveRecord
{
    public function getPrimaryKeys()
    {
        return ['flag_code', 'mail_code'];
    }

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Flag the static model class
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
		return 'flag_run_email';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array();
	}
}