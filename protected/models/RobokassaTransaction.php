<?php

/**
 * This is the model class for table "robokassa_transaction".
 *
 * The followings are the available columns in table 'robokassa_transaction':
 * @property integer $id
 * @property string $user_id
 * @property string $request_body
 * @property string $description
 * @property string $amount
 * @property integer $invoice_id
 * @property string $request
 * @property string $created_at
 * @property string $displayed_at
 * @property string $widget_body
 * @property string $processed_at
 * @property string $response_body
 */
class RobokassaTransaction extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return RobokassaTransaction the static model class
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
        return 'robokassa_transaction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, invoice_id', 'required'),
            array('invoice_id', 'numerical', 'integerOnly'=>true),
            array('user_id, amount', 'length', 'max'=>10),
            array('description', 'length', 'max'=>100),
            array('request', 'length', 'max'=>15),
            array('request_body, created_at, displayed_at, widget_body, processed_at, response_body', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, request_body, description, amount, invoice_id, request, created_at, displayed_at, widget_body, processed_at, response_body', 'safe', 'on'=>'search'),
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
            'request_body' => 'Request Body',
            'description' => 'Description',
            'amount' => 'Amount',
            'invoice_id' => 'Invoice',
            'request' => 'Request',
            'created_at' => 'Created At',
            'displayed_at' => 'Displayed At',
            'widget_body' => 'Widget Body',
            'processed_at' => 'Processed At',
            'response_body' => 'Response Body',
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
        $criteria->compare('request_body',$this->request_body,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('invoice_id',$this->invoice_id);
        $criteria->compare('request',$this->request,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('displayed_at',$this->displayed_at,true);
        $criteria->compare('widget_body',$this->widget_body,true);
        $criteria->compare('processed_at',$this->processed_at,true);
        $criteria->compare('response_body',$this->response_body,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}