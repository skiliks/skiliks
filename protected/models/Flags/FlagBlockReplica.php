<?php
/**
 * The followings are the available columns in table 'flag_run_email':
 * @property string $flag_code
 * @property integer $replica_id
 * @property integer $value
 */
class FlagBlockReplica extends CActiveRecord
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
		return 'flag_block_replica';
	}

    /**
     * @return array of string
     */
    public function primaryKey() {
        return array('flag_code', 'replica_id');
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'replica' => array(self::BELONGS_TO, 'Replica', 'replica_id'),
		);
	}
}