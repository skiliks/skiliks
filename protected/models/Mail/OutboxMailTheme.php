<?php

/**
 * Тема для исходящего письма
 *
 * The followings are the available columns in table 'outbox_mail_themes':
 * @property integer $id
 * @property integer $theme_id id  темы
 * @property integer $character_to_id id персонажа кому направлена тема
 * @property integer $mail_constructor_id id конструктора письма для этой темы
 * @property string  $import_id
 * @property string  $wr
 * @property string  $mail_prefix
 * @property string  $mail_code
 * @property integer $scenario_id
 *
 * The followings are the available model relations:
 * @property Character $characterTo
 * @property MailConstructor $mailConstructor
 * @property Scenario $scenario
 * @property Theme $theme
 */
class OutboxMailTheme extends CActiveRecord
{
    const SLUG_RIGHT = 'R';
    const SLUG_WRONG = 'W';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return OutboxMailTheme the static model class
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
        return 'outbox_mail_themes';
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
            array('theme_id, character_to_id, mail_constructor_id, scenario_id', 'numerical', 'integerOnly'=>true),
            array('import_id', 'length', 'max'=>14),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, theme_id, character_to_id, mail_constructor_id, import_id, scenario_id', 'safe', 'on'=>'search'),
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
            'characterTo' => array(self::BELONGS_TO, 'Character', 'character_to_id'),
            'mailConstructor' => array(self::BELONGS_TO, 'MailConstructor', 'mail_constructor_id'),
            'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
            'theme' => array(self::BELONGS_TO, 'Theme', 'theme_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'theme_id' => 'Theme',
            'character_to_id' => 'Character To',
            'mail_constructor_id' => 'Mail Constructor',
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
        $criteria->compare('theme_id',$this->theme_id);
        $criteria->compare('character_to_id',$this->character_to_id);
        $criteria->compare('mail_constructor_id',$this->mail_constructor_id);
        $criteria->compare('import_id',$this->import_id,true);
        $criteria->compare('scenario_id',$this->scenario_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Проверяет на блокировку тем для написания письма
     * @param Simulation $simulation
     * @return bool
     */
    public function isBlockedByFlags(Simulation $simulation) {

        $flagsDependence = $simulation->game_type->getFlagOutboxMailThemeDependencies(['outbox_mail_theme_id'=>$this->id]);
        if(empty($flagsDependence)){
            return false;
        }
        foreach($flagsDependence as $flagDependence) {
            $flagSimulation = FlagsService::getFlag($simulation, $flagDependence->flag_code);
            if($flagSimulation->value !== $flagDependence->value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверяет было отправлено письмо или нет
     * @param Simulation $simulation
     * @return bool
     */
    public function themeIsUsed(Simulation $simulation) {
        $mail = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'theme_id' => $this->theme_id,
            'receiver_id' => $this->character_to_id,
            'mail_prefix' => $this->mail_prefix
        ]);
        return null !== $mail;
    }
}