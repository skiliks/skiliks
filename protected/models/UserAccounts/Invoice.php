<?php

/**
 * This is the model class for table "invoice".
 *
 * The followings are the available columns in table 'invoice':
 * @property integer $id
 * @property string $user_id
 * @property integer $tariff_id
 * @property string $status
 * @property string $inn
 * @property string $cpp
 * @property string $account
 * @property string $bic
 * @property string $created_at
 * @property string $updated_at
 * @property string $comment
 *
 * The followings are the available model relations:
 * @property Tariff $tariff
 * @property YumUser $user
 */
class Invoice extends CActiveRecord
{
    const STATUS_PENDING  = 'pending';
    const STATUS_PAID     = 'paid';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED  = 'expired';

    public $agreeWithTerms = false;

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

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, tariff_id', 'required'),
			array('agreeWithTerms', 'required', 'on' => 'insert', 'message' => Yii::t('site', 'Accept terms of conditions')),
            array('inn', 'required', 'message' => Yii::t('site', 'INN is required')),
            array('inn', 'checkInn'),
            array('cpp', 'required', 'message' => Yii::t('site', 'CPP is required')),
            array('cpp', 'checkCpp'),
            array('account', 'required', 'message' => Yii::t('site', 'Account number is required')),
            array('account', 'checkAccount'),
            array('bic', 'required', 'message' => Yii::t('site', 'BIC is required')),
            array('bic', 'checkBic'),
			array('tariff_id', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>10),
			array('status', 'length', 'max'=>20),
			array('inn, cpp, account, bic', 'length', 'max'=>50),
			array('updated_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, tariff_id, status, inn, cpp, account, bic, created_at, updated_at', 'safe', 'on'=>'search'),
		);
	}

    public function checkInn()
    {
        $prefix = +substr($this->inn, 0, 4);
        $correct = preg_match('/^\d{10}$/', $this->inn);
        $correct = $correct && ($prefix >= 100 && $prefix <= 8399 || $prefix === 9909);

        if (!$correct) {
            $this->addError('inn', Yii::t('site', 'Wrong INN'));
        }
    }

    public function checkCpp()
    {
        $prefix = +substr($this->cpp, 0, 2);
        $correct = preg_match('/^\d{9}$/', $this->cpp);
        $correct = $correct && ($prefix >= 1 && $prefix <= 83 || $prefix === 99);

        if (!$correct) {
            $this->addError('cpp', Yii::t('site', 'Wrong CPP'));
        }
    }

    public function checkAccount()
    {
        $correct = preg_match('/^\d{5}(?:810|643)\d{12}$/', $this->account);
        if (!$correct) {
            $this->addError('account', Yii::t('site', 'Wrong account number'));
        }
    }

    public function checkBic()
    {
        $prefix = +substr($this->bic, 0, 2);
        $suffix = +substr($this->bic, 6, 3);
        $correct = preg_match('/^\d{9}$/', $this->bic);
        $correct = $correct && $prefix === 4 && $suffix >= 50 && $suffix <= 999;

        if (!$correct) {
            $this->addError('bic', Yii::t('site', 'Wrong BIC'));
        }
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'tariff_id' => 'Tariff',
			'status' => 'Status',
			'inn' => 'Inn',
			'cpp' => 'Cpp',
			'account' => 'Account',
			'bic' => 'Bic',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
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
		$criteria->compare('status',$this->status,true);
		$criteria->compare('inn',$this->inn,true);
		$criteria->compare('cpp',$this->cpp,true);
		$criteria->compare('account',$this->account,true);
		$criteria->compare('bic',$this->bic,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function behaviors(){
        return [
            'CTimestampBehavior' => [
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created_at',
                'updateAttribute' => 'updated_at'
            ]
        ];
    }
}