<?php

/**
 * This is the model class for table "flag_communication_theme_dependence".
 *
 * The followings are the available columns in table 'flag_communication_theme_dependence':
 * @property integer $id
 * @property integer $communication_theme_id
 * @property string $flag_code
 * @property integer $scenario_id
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property Scenario $scenario
 * @property CommunicationTheme $communicationTheme
 * @property Flag $flagCode
 */
class FlagCommunicationThemeDependence extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlagCommunicationThemeDependence the static model class
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
        return 'flag_communication_theme_dependence';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('communication_theme_id, flag_code, scenario_id, import_id', 'required'),
            array('communication_theme_id, scenario_id', 'numerical', 'integerOnly'=>true),
            array('flag_code', 'length', 'max'=>10),
            array('import_id', 'length', 'max'=>60),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, communication_theme_id, flag_code, scenario_id, import_id', 'safe', 'on'=>'search'),
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
            'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
            'communicationTheme' => array(self::BELONGS_TO, 'CommunicationTheme', 'communication_theme_id'),
            'flagCode' => array(self::BELONGS_TO, 'Flag', 'flag_code'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'communication_theme_id' => 'Communication Theme',
            'flag_code' => 'Flag Code',
            'scenario_id' => 'Scenario',
            'import_id' => 'Import',
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
        $criteria->compare('communication_theme_id',$this->communication_theme_id);
        $criteria->compare('flag_code',$this->flag_code,true);
        $criteria->compare('scenario_id',$this->scenario_id);
        $criteria->compare('import_id',$this->import_id,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}