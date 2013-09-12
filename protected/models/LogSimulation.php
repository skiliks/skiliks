<?php

/**
 * This is the model class for table "log_simulation".
 *
 * The followings are the available columns in table 'log_simulation':
 * @property integer $id
 * @property integer $invite_id
 * @property integer $sim_id
 * @property integer $user_id
 * @property string $scenario_name
 * @property string $mode
 * @property string $action
 * @property string $real_date
 * @property string $game_time_frontend
 * @property string $game_time_backend
 *
 * @property YumUser $user
 */
class LogSimulation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogSimulation the static model class
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
		return 'log_simulation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invite_id, sim_id, user_id', 'numerical', 'integerOnly'=>true),
			array('scenario_name, mode', 'length', 'max'=>20),
			array('action, real_date, game_time_frontend, game_time_backend', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, invite_id, sim_id, user_id, scenario_name, mode, action, real_date, game_time_frontend, game_time_backend', 'safe', 'on'=>'search'),
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
            'user'       => [self::BELONGS_TO, 'YumUser', 'user_id'],
            'simulation' => [self::BELONGS_TO, 'Simulation', 'simulation_id'],
            'invite'     => [self::BELONGS_TO, 'Invite', 'invite_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'invite_id' => 'Invite',
			'sim_id' => 'Sim',
			'user_id' => 'User',
			'scenario_name' => 'Scenario Name',
			'mode' => 'Mode',
			'action' => 'Action',
			'real_date' => 'Read Date',
			'game_time_frontend' => 'Game Time Frontend',
			'game_time_backend' => 'Game Time Backend',
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
		$criteria->compare('invite_id',$this->invite_id);
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('scenario_name',$this->scenario_name,true);
		$criteria->compare('mode',$this->mode,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('real_date',$this->read_date,true);
		$criteria->compare('game_time_frontend',$this->game_time_frontend,true);
		$criteria->compare('game_time_backend',$this->game_time_backend,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}