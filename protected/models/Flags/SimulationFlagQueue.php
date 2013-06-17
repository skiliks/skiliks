<?php

/**
 * This is the model class for table "simulation_flag_queue".
 *
 * The followings are the available columns in table 'simulation_flag_queue':
 * @property integer $id
 * @property integer $sim_id
 * @property string $flag_code
 * @property string $switch_time
 * @property integer $is_processed
 *
 * The followings are the available model relations:
 * @property Flag $flagCode
 * @property Simulation $sim
 */
class SimulationFlagQueue extends CActiveRecord
{
    const DONE = 1;
    const NONE = 0;
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return SimulationFlagQueue the static model class
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
        return 'simulation_flag_queue';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('sim_id, flag_code', 'required'),
            array('sim_id, status', 'numerical', 'integerOnly'=>true),
            array('flag_code', 'length', 'max'=>10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sim_id, flag_code, status', 'safe', 'on'=>'search'),
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
            'flagCode' => array(self::BELONGS_TO, 'Flag', 'flag_code'),
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
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
            'flag_code' => 'Flag Code',
            'switch_time' => 'Switch Time',
            'status' => 'Status',
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
        $criteria->compare('flag_code',$this->flag_code,true);
        $criteria->compare('switch_time',$this->switch_time);
        $criteria->compare('status',$this->status);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}