<?php

/**
 * This is the model class for table "log_server_request".
 *
 * The followings are the available columns in table 'log_server_request':
 * @property integer $id
 * @property integer $sim_id
 * @property string $request_uid
 * @property string $request_url
 * @property string $request_body
 * @property string $response_body
 * @property string $frontend_game_time
 * @property string $backend_game_time
 * @property string $real_time
 * @property integer $is_processed
 *
 * The followings are the available model relations:
 * @property Simulation $simulation
 */
class LogServerRequest extends CActiveRecord
{
    const IS_PROCESSED_TRUE = 1;
    const IS_PROCESSED_FALSE = 0;
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return LogServerRequest the static model class
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
        return 'log_server_request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('sim_id, request_uid, request_url, frontend_game_time, backend_game_time, real_time', 'required', 'on'=>'WithSimulation'),
            array('request_uid, request_url, frontend_game_time, real_time', 'required', 'on'=>'WithoutSimulation'),
            array('sim_id, is_processed', 'numerical', 'integerOnly'=>true),
            array('request_uid, request_url', 'length', 'max'=>100),
            array('request_body, response_body', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sim_id, request_uid, request_url, request_body, response_body, frontend_game_time, backend_game_time, real_time, is_processed', 'safe', 'on'=>'search'),
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
            'simulation' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'sim_id' => 'Sim',
            'request_uid' => 'Request Uid',
            'request_url' => 'Request Url',
            'request_body' => 'Request Body',
            'response_body' => 'Response Body',
            'frontend_game_time' => 'Frontend Game Time',
            'backend_game_time' => 'Backend Game Time',
            'real_time' => 'Real Time',
            'is_processed' => 'Is Processed',
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
        $criteria->compare('sim_id',$this->sim_id);
        $criteria->compare('request_uid',$this->request_uid,true);
        $criteria->compare('request_url',$this->request_url,true);
        $criteria->compare('request_body',$this->request_body,true);
        $criteria->compare('response_body',$this->response_body,true);
        $criteria->compare('frontend_game_time',$this->frontend_game_time,true);
        $criteria->compare('backend_game_time',$this->backend_game_time,true);
        $criteria->compare('real_time',$this->real_time,true);
        $criteria->compare('is_processed',$this->is_processed);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}