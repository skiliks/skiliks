<?php
/**
 * The followings are the available columns in table 'simulation_flag_queue':
 * @property integer $id
 * @property integer $sim_id
 * @property string $flag_code
 * @property string $switch_time
 * @property integer $is_processed
 * @property integer $value
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
        return array(
            array('sim_id, flag_code', 'required'),
            array('sim_id, is_processed, value', 'numerical', 'integerOnly'=>true),
            array('flag_code', 'length', 'max'=>10),
            array('id, sim_id, flag_code, is_processed', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
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
            'id'           => 'ID',
            'sim_id'       => 'Sim',
            'flag_code'    => 'Flag Code',
            'switch_time'  => 'Switch Time',
            'is_processed' => 'Processed',
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
        $criteria->compare('sim_id',$this->sim_id);
        $criteria->compare('flag_code',$this->flag_code,true);
        $criteria->compare('switch_time',$this->switch_time);
        $criteria->compare('is_processed',$this->is_processed);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}