<?php

/**
 * This is the model class for table "activity".
 *
 * The followings are the available columns in table 'activity':
 * @property string $id
 * @property string $parent
 * @property string $grandparent
 * @property string $name
 * @property integer $category_id
 * @prorerty string $import_id
 *
 * The followings are the available model relations:
 * @property ActivityAction[] $activityActions
 */
class Activity extends CActiveRecord
{
    /**
     * Activiti code, unique for activity
     * @var string
     */
    public $id;
    
    /**
     * Activiti id in numeric format, not uniq for activity
     * @var string
     */
    public $numeric_id;
    
    /**
     * Parrent code, not uniq for activity
     * @var string
     */
    public $parent;
    
    /**
     * Grandparrent code, not uniq for activity
     * @var string
     */
    public $grandparent;
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var string
     */
    public $type;
    
    /**
     * Importance.
     * From 0 to 5. 0 - very important, 5 - trash.
     * 2_min - must be act in 3 real time seconds.
     * @var integer
     */
    public $category_id;
    
    /**
     * Systen value to check is entity just imported or old after reimport and delete olds.
     * @var string
     */
    public $import_id;

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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('parent',$this->parent,true);
        $criteria->compare('grandparent',$this->grandparent,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('category_id',$this->category_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}