<?php

/**
 * This is the model class for table "site_log_generate_consolidated_analytic_file".
 *
 * The followings are the available columns in table 'site_log_generate_consolidated_analytic_file':
 * @property integer $id
 * @property string $started_at
 * @property string $finished_at
 * @property string $started_by_id
 * @property string $result
 *
 * @property YumUser $startedBy
 */
class SiteLogGenerateConsolidatedAnalyticFile extends CActiveRecord
{

    /**
     * @param string $assessment_version, 'v1' or 'v2'
     * @param SiteLogGenerateConsolidatedAnalyticFile $log
     * @return array
     */
    public static function generate($assessment_version, $log = null) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 60*60*3); // 3h

        // Собираем процентили {
        // по ним мы будем определять это реальная симуляция или прохождение тестировщика/разбаботчика
        $percentiles = AssessmentOverall::model()->findAllByAttributes([
            'assessment_category_code' => AssessmentCategory::PERCENTILE,
        ]);

        foreach($percentiles as $percentile) {
            $simulationPercentiles[$percentile->sim_id] = $percentile->value;
        }
        // Собираем процентили }

        // Собираем все симуляции и группируем по типу оценки [v1,v2] {
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);

        /* @var Simulation[] $simulations */
        $allSimulations = Simulation::model()->findAll("scenario_id = {$scenario->id} AND end IS NOT NULL");

        $realUserSimulationsV1 = [];
        $realUserSimulationsV2 = [];

        foreach($allSimulations as $simulation) {

            /* @var Simulation $simulation */
            if(isset($simulationPercentiles[$simulation->id])
                && $simulationPercentiles[$simulation->id] != 0
                && empty($simulation->results_popup_cache) === false) {

                /* @var Simulation $simulation */
                if (Simulation::ASSESSMENT_VERSION_1 == $simulation->assessment_version
                    /*&& count($realUserSimulationsV1) < 3*/) {
                    $realUserSimulationsV1[$simulation->id] = $simulation;
                }

                if (Simulation::ASSESSMENT_VERSION_2 == $simulation->assessment_version
                    /*&& count($realUserSimulationsV2) < 3*/) {
                    $realUserSimulationsV2[$simulation->id] = $simulation;
                }
            }

            if (12935 == $simulation->id) {
                var_dump(isset($realUserSimulationsV2[$simulation->id));
                die('1');
            }
        }
        die('2');
        // Собираем и группируем симуляции }

        // также нам нужны симуляции от e.sarnova@august-bel.by {
        $augustBelProfile = YumProfile::model()->findByAttributes(['email' => 'e.sarnova@august-bel.by']);
        if (null !== $augustBelProfile) {
            $augustBelSimulations = Simulation::model()->findAll(
                "user_id = {$augustBelProfile->user_id} and
                scenario_id = {$scenario->id} and
                assessment_version = '{$assessment_version}' and
                end is not null");

            foreach($augustBelSimulations as $simulation) {
                /* @var Simulation $simulation */
                if (Simulation::ASSESSMENT_VERSION_1 == $simulation->assessment_version) {
                    $realUserSimulationsV1[$simulation->id] = $simulation;
                }

                if (Simulation::ASSESSMENT_VERSION_2 == $simulation->assessment_version) {
                    $realUserSimulationsV2[$simulation->id] = $simulation;
                }
            }
        }
        // также нам нужны симуляции от e.sarnova@august-bel.by }

        // также нам нужна симуляця для o.zaikina@erc.ur.ru {
        $zaikinaProfile = YumProfile::model()->findByAttributes(['email' => 'o.zaikina@erc.ur.ru']);
        if (null !== $zaikinaProfile) {
            $zaikinaSimulations = Simulation::model()->findAll(
                "user_id = {$zaikinaProfile->user_id} and
                scenario_id = {$scenario->id} and
                assessment_version = '{$assessment_version}' and
                end is not null");

            foreach($zaikinaSimulations as $simulation) {
                /* @var Simulation $simulation */
                if (Simulation::ASSESSMENT_VERSION_1 == $simulation->assessment_version) {
                    $realUserSimulationsV1[$simulation->id] = $simulation;
                }

                if (Simulation::ASSESSMENT_VERSION_2 == $simulation->assessment_version) {
                    $realUserSimulationsV2[$simulation->id] = $simulation;
                }
            }
        }
        // также нам нужна симуляця для o.zaikina@erc.ur.ru }

        ksort($realUserSimulationsV1);
        ksort($realUserSimulationsV2);

        // непосредственно генерация
        $generator = new AnalyticalFileGenerator();
        $generator->is_add_behaviours = true;
        $generator->oneStepProgress = 0.2;
        $generator->createDocument();
        $generator->totalV1SimsAmount = count($realUserSimulationsV1);
        $generator->totalV2SimsAmount = count($realUserSimulationsV2);

        $generator->runAssessment_v1($realUserSimulationsV1, 'v1_to_v2', $log);

        $generator->runAssessment_v2($realUserSimulationsV2, $log);
        $generator->setAutoFilters();
        $generator->save('','full_report');

        return [
            'v1' => $realUserSimulationsV1,
            'v2' => $realUserSimulationsV2
        ];
    }

    // ---------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SiteLogGenerateConsolidatedAnalyticFile the static model class
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
		return 'site_log_generate_consolidated_analytic_file';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('started_by_id', 'length', 'max'=>10),
			array('started_at, finished_at, result', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, started_at, finished_at, started_by_id, status, result', 'safe', 'on'=>'search'),
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
            'startedBy' => array(self::BELONGS_TO, 'YumUser', 'started_by_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'started_at' => 'Started At',
			'finished_at' => 'Finished At',
			'started_by_id' => 'Started By',
			'result' => 'Result',
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
		$criteria->compare('started_at',$this->started_at,true);
		$criteria->compare('finished_at',$this->finished_at,true);
		$criteria->compare('started_by_id',$this->started_by_id,true);
		$criteria->compare('result',$this->result,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}