<?php

class LearningAreaAnalizer {

    public $simulation;

    public function __construct($simulation) {

        $this->simulation = $simulation;

    }

    public function run() {
        $this->stressResistance();
        $this->stability();
        $this->responsibility();
        $this->resultOrientation();
    }

    public function stressResistance() {

        /*
         * AssessmentAggregated 7141
         */
        /* @var $simulation Simulation */
        /* @var $game_type Scenario */
        $simulation = $this->simulation;
        $game_type = $simulation->game_type;
        $point = $game_type->getHeroBehaviour(['code' => 7141]);
        if (null === $point) {
            return;
        }

        //$point = HeroBehaviour::model()->findByAttributes(['code'=>7141]);

        /* @var $stress StressPoint[] */
        $stress = StressPoint::model()->findAllByAttributes(['sim_id'=>$this->simulation->id]);

        if(null !== $stress) {
            $value = 0;
            foreach( $stress as $stress_rule ) {
                $value += $stress_rule->stressRule->value;
            }
        } else {
            $value = 0;
        }

        $assessment = new AssessmentAggregated();
        $assessment->point_id = $point->id;
        $assessment->sim_id = $this->simulation->id;
        $assessment->value = round($value, 2);
        $assessment->save();

        /*
         * StressResistance 7141
         */
        /* @var $max_rate MaxRate */
        $max_rate = $game_type->getMaxRate(['hero_behaviour_id'=>$point->id]);

        $value = round(($value / $max_rate->rate) * 100, 2);

        $learning_area = $game_type->getLearningArea(['code' => 9]);//Стрессоустойчивость
        $sim_learning_area = new SimulationLearningArea();
        $sim_learning_area->learning_area_id = $learning_area->id;
        $sim_learning_area->value = ($value > 100)?100:$value;
        $sim_learning_area->sim_id = $simulation->id;
        $sim_learning_area->save();


    }

    public function stability() {

        /* @var $simulation Simulation */
        /* @var $game_type Scenario */
        $simulation = $this->simulation;
        $game_type = $simulation->game_type;
        $point = $game_type->getHeroBehaviour(['code' => 7211]);
        if (null === $point) {
            return;
        }
        $value = AssessmentAggregated::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$point->id]);
        if(null === $value){
            $value = 0;
        }

        $max_rate = $game_type->getMaxRate(['hero_behaviour_id' => $point->id]);

        $value = round(($value / $max_rate->rate) * 100, 2);

        $learning_area = $game_type->getLearningArea(['code' => 10]);//Устойчивость к манипуляциям и давлению
        $sim_learning_area = new SimulationLearningArea();
        $sim_learning_area->learning_area_id = $learning_area->id;
        $sim_learning_area->value = ($value > 100)?100:$value;
        $sim_learning_area->sim_id = $simulation->id;
        $sim_learning_area->save();

    }

    public function responsibility(){

        /* @var $simulation Simulation */
        /* @var $game_type Scenario */
        $simulation = $this->simulation;
        $game_type = $simulation->game_type;
        $point_8212 = $game_type->getHeroBehaviour(['code' => 8212]);
        $point_8213 = $game_type->getHeroBehaviour(['code' => 8213]);
        $point_821 = $game_type->getHeroBehaviour(['code' => 821]);
        if(null === $point_8212 OR null === $point_8213 OR null === $point_821){
            return;
        }

        $value_8212 = AssessmentAggregated::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$point_8212->id]);
        if(null === $value_8212){
            $value_8212 = 0;
        }

        $value_8213 = AssessmentAggregated::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$point_8213->id]);
        if(null === $value_8213){
            $value_8213 = 0;
        }

        $value = $value_8212 + $value_8213;

        $max_rate = $game_type->getMaxRate(['hero_behaviour_id' => $point_821->id]);

        $value = round(($value / $max_rate->rate) * 100, 2);

        $learning_area = $game_type->getLearningArea(['code' => 12]);//Ответственность
        $sim_learning_area = new SimulationLearningArea();
        $sim_learning_area->learning_area_id = $learning_area->id;
        $sim_learning_area->value = ($value > 100)?100:$value;
        $sim_learning_area->sim_id = $simulation->id;
        $sim_learning_area->save();

    }

    public function resultOrientation() {

        /* @var $simulation Simulation */
        /* @var $game_type Scenario */
        $simulation = $this->simulation;
        $game_type = $simulation->game_type;
        $point = $game_type->getHeroBehaviour(['code' => 8371]);
        if (null === $point) {
            return;
        }
        $value = AssessmentAggregated::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$point->id]);
        if(null === $value){
            $value = 0;
        }

        $max_rate = $game_type->getMaxRate(['hero_behaviour_id' => $point->id]);

        $value = round(($value / $max_rate->rate) * 100, 2);

        $learning_area = $game_type->getLearningArea(['code' => 14]);//Ориентация на результат
        $sim_learning_area = new SimulationLearningArea();
        $sim_learning_area->learning_area_id = $learning_area->id;
        $sim_learning_area->value = ($value > 100)?100:$value;
        $sim_learning_area->sim_id = $simulation->id;
        $sim_learning_area->save();

    }

}