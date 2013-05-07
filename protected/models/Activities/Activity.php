<?php

/**
 * This is the model class for table "activity".
 *
 * The followings are the available columns in table 'activity':
 * @property integer $id
 * @property string $parent
 * @property string $grandparent
 * @property string $name
 * @property string $code
 * @property string $type
 * @property string $category_id
 * @property string $numeric_id
 * @property string $import_id
 * @property integer $scenario_id
 *
 * @property ActivityParent parentActivity
 * @property ActivityCategory category
 *
 * The followings are the available model relations:
 * @property ActivityAction[] $activityActions
 */
class Activity extends CActiveRecord
{
    // Place your specific code there

    // ---------------------------------------------------------------------------------------------------

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Activity the static model class
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
        return 'activity';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('parent, grandparent, name', 'required'),
            array('category_id', 'length', 'max'=>10),
            array('id, parent, grandparent', 'length', 'max'=>255),
            array('name', 'length', 'max'=>255),
            array('import_id', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, parent, grandparent, name, category_id, import_id', 'safe', 'on'=>'search'),
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
            'category' => array(self::BELONGS_TO, 'ActivityCategory', 'category_id'),
            'activityActions' => array(self::HAS_MANY, 'ActivityAction', 'activity_id'),
            'parentActivity' => [self::BELONGS_TO, 'ActivityParent', 'parent']
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'parent' => 'Parent',
            'grandparent' => 'Grandparent',
            'name' => 'Name',
            'category_id' => 'Category',
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
        $criteria->compare('parent',$this->parent);
        $criteria->compare('grandparent',$this->grandparent);
        $criteria->compare('name',$this->name);
        $criteria->compare('category_id',$this->category_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}