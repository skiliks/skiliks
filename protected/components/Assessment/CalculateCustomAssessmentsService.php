<?php

/**
 * Оценки 3312 и 341a8 при переключении звуков
 * в игре
 * Class CalculateCustomAssessmentsService
 */
class CalculateCustomAssessmentsService {

    /**
     * @var null|Simulation
     */
    public $simulation = null;

    /**
     * @param Simulation $simulation
     */
    public function __construct(Simulation $simulation) {
        $this->simulation = $simulation;
    }

    /**
     * Запуск расчета оценки
     */
    public function run() {
        $this->check_3312();
        $this->check_341a8();
    }

    /**
     * Логирование переключения звука в почте и телефоне
     * @param $logs логи
     * @return float|int
     * @throws LogicException
     */
    protected function calcTurnOff($logs) {
        /* @var $log LogIncomingCallSoundSwitcher */
        $start_time = 0;
        $duration = 0;
        if(count($logs) === 0) {
            return 0;
        } else if(count($logs) === 1) {
            return (strtotime('18:00:00') - strtotime($logs[0]->game_time))/60;
        }
        foreach($logs as $log) {

            if($log->is_play == LogIncomingCallSoundSwitcher::IS_PLAY_FALSE) {
                $start_time = strtotime($log->game_time);
            } elseif($log->is_play == LogIncomingCallSoundSwitcher::IS_PLAY_TRUE) {
                $duration += strtotime($log->game_time) - $start_time;
            } else {
                throw new LogicException("Not found '{$log->is_play}' ");
            }
        }
        return $duration/60;
    }

    /**
     * Рачет повидения 3312
     */
    protected function check_3312() {

        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code'=>'3312']);

        $logs = LogIncomingCallSoundSwitcher::model()->findAllByAttributes([
            'sim_id'=>$this->simulation->id,
            'sound_alias'=>LogIncomingCallSoundSwitcher::INCOMING_MAIL
        ]);


            $duration = $this->calcTurnOff($logs);
            $skiliksSpeedFactor = Yii::app()->params['public']['skiliksSpeedFactor'];
            if($skiliksSpeedFactor*5 <= $duration){
                $value = $behaviour->scale;
            }else{
                $value = 0;
            }

        $this->saveAssessment($behaviour, $value);

    }

    /**
     * Рачет повидения 341a8
     */
    protected function check_341a8() {

        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code'=>'341a8']);

        $logs = LogIncomingCallSoundSwitcher::model()->findAllByAttributes([
            'sim_id'=>$this->simulation->id,
            'sound_alias'=>LogIncomingCallSoundSwitcher::INCOMING_CALL
        ]);


        $duration = $this->calcTurnOff($logs);
        $skiliksSpeedFactor = Yii::app()->params['public']['skiliksSpeedFactor'];
        if($skiliksSpeedFactor*5 <= $duration){
            $value = $behaviour->scale;
        }else{
            $value = 0;
        }

        $this->saveAssessment($behaviour, $value);

    }


    /**
     * Сохраняет оценку
     * @param HeroBehaviour $behaviour
     * @param $value оценка
     * @throws Exception
     */
    protected function saveAssessment(HeroBehaviour $behaviour, $value) {
        $assessment_calculation = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$this->simulation->id, 'point_id'=>$behaviour->id]);
        if(null === $assessment_calculation){
            $assessment_calculation = new AssessmentCalculation();
        }
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simulation->id;
        if(false === $assessment_calculation->save()){
            throw new Exception("Not save");
        }
    }

}