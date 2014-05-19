<?php

/**
 * This is the model class for table "flag_block_dialod".
 *
 * The followings are the available columns in table
 * @property string $flag_code
 * @property string $dialog_code
 * @property integer $value
 */
class FlagBlockDialog extends CActiveRecord
{
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
		return 'flag_block_dialog';
	}

    /**
     * @return array of string
     */
    public function primaryKey() {
        return array('flag_code', 'dialog_code');
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array();
	}
}