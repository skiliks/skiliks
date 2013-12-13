<?php

/**
 * This is the model class for table "window".
 *
 * The followings are the available columns in table 'window':
 * @property integer $id
 * @property string $type
 * @property string $subtype
 */
class Window extends CActiveRecord implements IGameAction
{
    const PHONE_TALK = "phone talk";
    const MAIL_NEW   =  "mail new";

    /**
     * @return string
     */
    public function getCode(){
        return $this->subtype;
    }


    /* ----------------------------------------------------------------------------------- */

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Window the static model class
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
        return 'window';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id', 'numerical', 'integerOnly'=>true),
            array('type, subtype', 'length', 'max'=>255),
            array('id, type, subtype', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id'      => 'ID',
            'type'    => 'Type',
            'subtype' => 'Subtype',
        );
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('type', $this->type);
        $criteria->compare('subtype', $this->subtype);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}
