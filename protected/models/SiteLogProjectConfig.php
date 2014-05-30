<?php

/**
 * This is the model class for table "site_log_project_config".
 *
 * The followings are the available columns in table 'site_log_project_config':
 * @property integer $id
 * @property string $user_id
 * @property string $project_config_id
 * @property string $created_at
 * @property string $log
 *
 * The followings are the available model relations:
 * @property ProjectConfig $projectConfig
 * @property YumUser $user
 */
class SiteLogProjectConfig extends CActiveRecord
{
    /**
     * Метод логирует в таблицу 'site_log_project_config' данные об изменениях в конфиге $config,
     * если они были сделаны
     *
     * @param YumUse $user, тот кто менял значения конфига
     * @param ProjectConfig $config
     */
    public static function log($user, $config) {

        $log = new SiteLogProjectConfig();
        $log->created_at = date('Y-m-d H:i:s');
        $log->user_id = $user->id;
        $log->project_config_id = $config->id;

        $newAttributes = $config->getAttributes();
        $config->refresh();
        $oldAttributes = ProjectConfig::model()->findByPk($config->id);

        $text = '';

        foreach ($newAttributes as $name => $value) {
            if (!empty($oldAttributes)) {
                $oldValue = $oldAttributes[$name];
            } else {
                $oldValue = null;
            }

            if ($value != $oldValue) {
                $text .= sprintf(
                    '&nbsp;&nbsp;&nbsp; * поле "%s" изменено с "%s" на "%s" <br/>',
                    $name,
                    $oldValue,
                    $value
                );

                $config->{$name} = $value;
            }
        }

        if ('' != $text) {
            $log->log = sprintf(
                'У конфига "%s" были изменены следующие поля: <br/> %s',
                $config->alias,
                $text
            );
            $log->save();
            $config->save();
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SiteLogProjectConfig the static model class
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
		return 'site_log_project_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, project_config_id', 'length', 'max'=>10),
			array('created_at, log', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, project_config_id, created_at, log', 'safe', 'on'=>'search'),
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
			'projectConfig' => array(self::BELONGS_TO, 'ProjectConfig', 'project_config_id'),
			'user' => array(self::BELONGS_TO, 'YumUser', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'project_config_id' => 'Project Config',
			'created_at' => 'Created At',
			'log' => 'Log',
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
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('project_config_id',$this->project_config_id,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('log',$this->log,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}