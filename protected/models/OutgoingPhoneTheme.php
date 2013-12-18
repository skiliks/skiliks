<?php

/**
 * Темы для исходящего телефонного звонка
 *
 * The followings are the available columns in table 'outgoing_phone_themes':
 * @property integer $id
 * @property integer $theme_id id темы
 * @property integer $character_to_id id Персонажа кому тема
 * @property string $wr Правильная(R), не правильная(W) и нейральная темы(N)
 * @property string $import_id
 * @property integer $scenario_id
 *
 * The followings are the available model relations:
 * @property Character $characterTo
 * @property Scenario $scenario
 * @property Theme $theme
 */
class OutgoingPhoneTheme extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return OutgoingPhoneTheme the static model class
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
        return 'outgoing_phone_themes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('scenario_id', 'required'),
            array('theme_id, character_to_id, scenario_id', 'numerical', 'integerOnly'=>true),
            array('wr', 'length', 'max'=>5),
            array('import_id', 'length', 'max'=>14),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, theme_id, character_to_id, wr, import_id, scenario_id', 'safe', 'on'=>'search'),
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
            'characterTo' => array(self::BELONGS_TO, 'Character', 'character_to_id'),
            'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
            'theme' => array(self::BELONGS_TO, 'Theme', 'theme_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'theme_id' => 'Theme',
            'character_to_id' => 'Character To',
            'wr' => 'Wr',
            'import_id' => 'Import',
            'scenario_id' => 'Scenario',
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
        $criteria->compare('theme_id',$this->theme_id);
        $criteria->compare('character_to_id',$this->character_to_id);
        $criteria->compare('wr',$this->wr,true);
        $criteria->compare('import_id',$this->import_id,true);
        $criteria->compare('scenario_id',$this->scenario_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}