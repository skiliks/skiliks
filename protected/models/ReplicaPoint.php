<?php

/**
 * This is the model class for table "characters_points".
 *
 * The followings are the available columns in table 'characters_points':
 * @property integer $id
 * @property integer $dialog_id
 * @property integer $point_id
 * @property integer $add_value
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property Replica $dialog
 * @property HeroBehaviour $point
 */
class ReplicaPoint extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ReplicaPoint the static model class
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
        return 'characters_points';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dialog_id, point_id, add_value', 'required'),
            array('dialog_id, point_id, add_value', 'numerical', 'integerOnly'=>true),
            array('import_id', 'length', 'max'=>14),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, dialog_id, point_id, add_value, import_id', 'safe', 'on'=>'search'),
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
            'dialog' => array(self::BELONGS_TO, 'Replica', 'dialog_id'),
            'pointTitle' => array(self::BELONGS_TO, 'HeroBehaviour', 'point_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'dialog_id' => 'Dialog',
            'point_id' => 'Point',
            'add_value' => 'Add Value',
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
        $criteria->compare('dialog_id',$this->dialog_id);
        $criteria->compare('point_id',$this->point_id);
        $criteria->compare('add_value',$this->add_value);
        $criteria->compare('import_id',$this->import_id,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @param string $ids
     * @return array of ReplicaPoint
     */
    public function byIdsNotIn($ids)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " `id` NOT IN ({$ids}) "
        ));
        return $this;
    }

    /**
     * Выборка оценки по конкретному диалогу
     * @param int $dialogId идентификатор диалога
     * @return ReplicaPoint
     */
    public function byDialog($dialogId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "dialog_id = '{$dialogId}'"
        ));
        return $this;
    }

    /**
     * Выборка по идентификатору оценки
     * @param int $pointId
     * @return ReplicaPoint
     */
    public function byPoint($pointId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "point_id = '{$pointId}'"
        ));
        return $this;
    }
}