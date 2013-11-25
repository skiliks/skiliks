<?php

/**
 * This is the model class for table "tariff_plan".
 *
 * The followings are the available columns in table 'tariff_plan':
 * @property integer $id
 * @property string $user_id
 * @property integer $tariff_id
 * @property string $invoice_id
 * @property string $started_at
 * @property string $finished_at
 * @property string $status
 *
 * The followings are the available model relations:
 * @property Invoice $invoice
 * @property Tariff $tariff
 * @property YumUser $user
 */
class TariffPlan extends CActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return TariffPlan the static model class
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
        return 'tariff_plan';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('tariff_id', 'numerical', 'integerOnly'=>true),
            array('user_id', 'length', 'max'=>10),
            array('invoice_id', 'length', 'max'=>11),
            array('status', 'length', 'max'=>15),
            array('started_at, finished_at', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, tariff_id, invoice_id, started_at, finished_at, status', 'safe', 'on'=>'search'),
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
            'invoice' => array(self::BELONGS_TO, 'Invoice', 'invoice_id'),
            'tariff' => array(self::BELONGS_TO, 'Tariff', 'tariff_id'),
            'user' => array(self::BELONGS_TO, 'YumUser', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'tariff_id' => 'Tariff',
            'invoice_id' => 'Invoice',
            'started_at' => 'Started At',
            'finished_at' => 'Finished At',
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
        $criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('tariff_id',$this->tariff_id);
        $criteria->compare('invoice_id',$this->invoice_id,true);
        $criteria->compare('started_at',$this->started_at,true);
        $criteria->compare('finished_at',$this->finished_at,true);
        $criteria->compare('status',$this->status,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

}