<?php

/**
 * This is the model class for table "invoice".
 *
 * The followings are the available columns in table 'invoice':
 * @property integer $id
 * @property string $user_id
 * @property integer $tariff_id
 * @property string $amount
 * @property string $create_date
 * @property string $paid_date
 * @property string $payment_system
 * @property string $additional_data
 * @property string $comment
 * @property integer $month_selected
 *
 * The followings are the available model relations:
 * @property Tariff $tariff
 * @property YumUser $user
 */
class Invoice extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Invoice the static model class
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
        return 'invoice';
    }

    public function getStatuses() {
        return [self::STATUS_PENDING, self::STATUS_PAID, self::STATUS_EXPIRED, self::STATUS_REJECTED];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, tariff_id', 'required'),
            array('user_id, tariff_id', 'safe', 'on'=>'search'),
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
            'tariff' => array(self::BELONGS_TO, 'Tariff', 'tariff_id'),
            'user' => array(self::BELONGS_TO, 'YumUser', 'user_id'),
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
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('create_date',$this->create_date,true);
        $criteria->compare('payment_system',$this->payment_system,true);
        $criteria->compare('paid_date',$this->paid_date,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Method add paid_date to Invoice and saves it
     */

    public function completeInvoice() {
        $this->paid_date = date('Y-m-d H:i:s');
        $this->save();
    }


    /**
     *
     * @param int $user_id
     * @param int $tariff_id
     * @param int $amount
     *
     * @return int|bool new invoice id or false
     *
     * Method need for creating an invoice and storing it do db
     */

    public function createInvoice($user = null, Tariff $tariff = null, $months = null) {
        if($user !== null && $tariff !== null) {
            $this->create_date = date('Y-m-d H:i:s');
            $this->user        = $user;
            $this->tariff      = $tariff;
            $this->user_id     = $user->id;
            $this->tariff_id   = $tariff->id;
            $this->amount      = $tariff->price * $months;
            $this->month_selected = $months;
            $this->save();
            return $this->id;
        }
        else return false;
    }


    /**
     * @return bool
     *
     * Method checks if invoice is complete
     */

    public function isComplete() {
        if($this->paid_date !== null) {
            return true;
        }
        else return false;
    }

    /**
     * @return string the name of payment method
     *
     * Method sets the name of the payment method to array
     */


    public function setPaymentMethod($ps = false) {
        if($ps) {
            $this->payment_system = $ps;
            $this->save();
            return true;
        }
        else return false;
    }

    /**
     * @return string additional data of payment method
     *
     * Method sets the additional data of payment method to an invoice
     */

    public function setAdditionalData($ad_data = false) {
        if($ad_data) {
            $this->additional_data = $ad_data;
            $this->save();
            return true;
        }
        else return false;
    }

}