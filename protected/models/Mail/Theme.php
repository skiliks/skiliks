<?php

/**
 *  Тема для исходящего звонка или исходящего письма
 *
 * The followings are the available columns in table 'theme':
 * @property integer $id
 * @property string $text Текст с листа All Themes
 * @property string $theme_code Код темы с листа All Themes колонка A Original_Theme_id
 * @property string $import_id
 * @property integer $scenario_id
 *
 * The followings are the available model relations:
 * @property Scenario $scenario
 */
class Theme extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Theme the static model class
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
        return 'theme';
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
            array('scenario_id', 'numerical', 'integerOnly'=>true),
            array('text', 'length', 'max'=>255),
            array('theme_code', 'length', 'max'=>10),
            array('import_id', 'length', 'max'=>14),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, text, theme_code, import_id, scenario_id', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'text' => 'Text',
            'theme_code' => 'Theme Code',
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
        $criteria->compare('text',$this->text,true);
        $criteria->compare('theme_code',$this->theme_code,true);
        $criteria->compare('import_id',$this->import_id,true);
        $criteria->compare('scenario_id',$this->scenario_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getFormattedTheme($prefix='') {
        return str_replace(['re', 'fwd'], ['Re: ', 'Fwd: '], $prefix) . $this->text;
    }
}