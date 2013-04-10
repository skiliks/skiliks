<?php

/**
 * This is the model class for table "time_management_aggregated".
 *
 * The followings are the available columns in table 'time_management_aggregated':
 * @property integer $id
 * @property integer $sim_id
 * @property string $slug
 * @property string $value
 * @property string $unit_label
 */
class TimeManagementAggregated extends CActiveRecord
{
    const SLUG_GLOBAL_TIME_SPEND_FOR_INACTIVITY              = 'time_spend_for_inactivity';
    const SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES = 'time_spend_for_1st_priority_activities';
    const SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES = 'time_spend_for_non_priority_activities';

    const SLUG_WORKDAY_OVERHEAD_DURATION = 'workday_overhead_duration';
    const SLUG_EFFICIENCY = 'efficiency';

    const SLUG_1ST_PRIORITY_DOCUMENTS   = '1st_priority_documents';
    const SLUG_1ST_PRIORITY_MEETINGS    = '1st_priority_meetings';
    const SLUG_1ST_PRIORITY_PHONE_CALLS = '1st_priority_phone_calls';
    const SLUG_1ST_PRIORITY_MAIL        = '1st_priority_mail';
    const SLUG_1ST_PRIORITY_PLANING     = '1st_priority_planning';

    const SLUG_NON_PRIORITY_DOCUMENTS   = 'non_priority_documents';
    const SLUG_NON_PRIORITY_MEETINGS    = 'non_priority_meetings';
    const SLUG_NON_PRIORITY_PHONE_CALLS = 'non_priority_phone_calls';
    const SLUG_NON_PRIORITY_MAIL        = 'non_priority_mail';
    const SLUG_NON_PRIORITY_PLANING     = 'non_priority_planning';

    /* --- */

    /**
     * @param string $slug
     * @return string
     */
    public static function getUnitLabel($slug)
    {
        if (self::SLUG_WORKDAY_OVERHEAD_DURATION == $slug) {
            return 'min';
        }

        return '%';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        switch ($this->slug) {
            case self::SLUG_EFFICIENCY :
                return 'Общая оценку эффективности использования времени';
                break;
            case self::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES :
                return 'Время потраченное на задачи 1-го приоритета';
                break;
            case self::SLUG_GLOBAL_TIME_SPEND_FOR_INACTIVITY :
                return 'Время ожидания или бездействия';
                break;
            case self::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES :
                return 'Время потраченное на не важные задачи';
                break;
            case self::SLUG_WORKDAY_OVERHEAD_DURATION :
                return 'Время на которое задержался на на работе';
                break;
            case self::SLUG_1ST_PRIORITY_DOCUMENTS :
                return 'Время потраченное на документы 1-го приоритета';
                break;
            case self::SLUG_1ST_PRIORITY_PLANING :
                return 'Время потраченное на планирование задачь 1-го приоритета';
                break;
            case self::SLUG_1ST_PRIORITY_MAIL :
                return 'Время потраченное на письма 1-го приоритета';
                break;
            case self::SLUG_1ST_PRIORITY_PHONE_CALLS :
                return 'Время потраченное на звонка 1-го приоритета';
                break;
            case self::SLUG_1ST_PRIORITY_MEETINGS :
                return 'Время потраченное на встречи 1-го приоритета';
                break;
            case self::SLUG_NON_PRIORITY_MAIL :
                return 'Время потраченное на не важные письма';
                break;
            case self::SLUG_NON_PRIORITY_PHONE_CALLS :
                return 'Время потраченное на не важные звонки';
                break;
            case self::SLUG_NON_PRIORITY_MEETINGS :
                return 'Время потраченное на не важные встречи';
                break;
            case self::SLUG_NON_PRIORITY_DOCUMENTS :
                return 'Время потраченное на не важные документы';
                break;
            case self::SLUG_NON_PRIORITY_PLANING :
                return 'Время потраченное на планирование не важных задачь';
                break;
            default:
                return '--';
        }
    }

    /* ------------------------------------------------------------------------------------------------------ */

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TimeManagementAggregated the static model class
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
		return 'time_management_aggregated';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, slug, value, unit_label', 'required'),
			array('sim_id', 'numerical', 'integerOnly'=>true),
			array('slug, unit_label', 'length', 'max'=>60),
			array('value', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, slug, value, unit_label', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sim_id' => 'Sim',
			'slug' => 'Slug',
			'value' => 'Value',
			'unit_label' => 'Unit Label',
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
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('unit_label',$this->unit_label,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}