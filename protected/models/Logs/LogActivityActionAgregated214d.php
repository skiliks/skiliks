<?php

/**
 * This is the model class for table "log_activity_action_agregated_214d".
 *
 * The followings are the available columns in table 'log_activity_action_agregated_214d':
 * @property integer $id
 * @property integer $sim_id
 * @property string $leg_type
 * @property string $leg_action
 * @property integer $activity_action_id
 * @property string $category
 * @property string $start_time
 * @property string $end_time
 * @property string $duration
 * @property integer $is_keep_last_category
 *
 * The followings are the available model relations:
 * @property Simulation $simulation
 * @property ActivityAction $activityAction
 */
class LogActivityActionAgregated214d extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return LogActivityActionAgregated214d the static model class
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
        return 'log_activity_action_agregated_214d';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('sim_id, start_time, end_time, duration', 'required'),
            array('sim_id, activity_action_id, is_keep_last_category', 'numerical', 'integerOnly'=>true),
            array('leg_type, leg_action, category', 'length', 'max'=>30),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sim_id, leg_type, leg_action, activity_action_id, category, start_time, end_time, duration, is_keep_last_category', 'safe', 'on'=>'search'),
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
            'simulation' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
            'activityAction' => array(self::BELONGS_TO, 'ActivityAction', 'activity_action_id'),
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
            'leg_type' => 'Leg Type',
            'leg_action' => 'Leg Action',
            'activity_action_id' => 'Activity Action',
            'category' => 'Category',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'duration' => 'Duration',
            'is_keep_last_category' => 'Is Keep Last Category',
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
        $criteria->compare('leg_type',$this->leg_type,true);
        $criteria->compare('leg_action',$this->leg_action,true);
        $criteria->compare('activity_action_id',$this->activity_action_id);
        $criteria->compare('category',$this->category,true);
        $criteria->compare('start_time',$this->start_time,true);
        $criteria->compare('end_time',$this->end_time,true);
        $criteria->compare('duration',$this->duration,true);
        $criteria->compare('is_keep_last_category',$this->is_keep_last_category);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}