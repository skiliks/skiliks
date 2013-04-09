<?php

class LearningAreaAnalyzer {

    public $simulation;

    public function __construct($simulation) {

        $this->simulation = $simulation;

    }

    public function run() {
        $this->stressResistance();
        $this->stability();
        $this->responsibility();
        $this->adoptionOfDecisions();
        $this->resultOrientation();
        $this->constructibility();
        $this->flexibility();
        $this->attentiveness();
    }

    public function calcLearningArea($simulation,  $code) {

        /* @var $game_type Scenario */
        $game_type = $simulation->game_type;
        $point = $game_type->getHeroBehaviour(['code' => $code]);
        if(null === $point){
            throw new HeroBehaviourIsNullException(" Not Found {$code} ");
        }
        $aggregated = AssessmentAggregated::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$point->id]);
        if(null === $aggregated){
            return 0;
        }else{
            return $aggregated->value;
        }

    }

    public function calcMaxRate($simulation, $code, $assessment){

        /* @var $game_type Scenario */
        $game_type = $simulation->game_type;
        if( !empty($code["learning_goal_code"]) ) {
            $point = $game_type->getLearningGoal(['code' => $code["learning_goal_code"]]);
            if( null === $point ){
                throw new LearningGoalIsNullException(" Not Found {$code["learning_goal_code"]} on LearningGoal ");
            }
            $rate = ['learning_goal_id' => $point->id];
        }elseif( !empty($code["hero_behaviour_code"]) ) {
            $point = $game_type->getHeroBehaviour(['code' => $code["hero_behaviour_code"]]);
            if(null === $point){
                throw new HeroBehaviourIsNullException(" Not Found {$code["hero_behaviour_code"]} on HeroBehaviour");
            }
            $rate = ['hero_behaviour_id' => $point->id];
        } else {
            throw new Exception("Parameter 'learning_goal_code' or 'hero_behaviour_code' is not found");
        }

        $game_type = $simulation->game_type;

        $max_rate = $game_type->getMaxRate($rate);

        return ($assessment / $max_rate->rate) * 100;
    }

    public function calcWeight($simulation, $code, $value) {

        /* @var $game_type Scenario */
        $game_type = $simulation->game_type;
        $point = $game_type->getHeroBehaviour(['code' => $code]);
        if(null === $point){
            throw new HeroBehaviourIsNullException(" Not Found {$code} ");
        }
        $weight = $game_type->getWeight(['hero_behaviour_id' => $point->id]);

        if(null === $weight){
            throw new HeroBehaviourIsNullException(" Not Found {$code} ");
        }

        return $weight->value*$value;

    }

    public function saveLearningArea($simulation, $code, $value){
        /* @var $game_type Scenario */
        $game_type = $simulation->game_type;
        $learning_area = $game_type->getLearningArea(['code' => $code]);//Стрессоустойчивость
        $sim_learning_area = new SimulationLearningArea();
        $sim_learning_area->learning_area_id = $learning_area->id;
        $sim_learning_area->value = ($value > 100)?100:$value;
        $sim_learning_area->sim_id = $simulation->id;
        $sim_learning_area->save();
    }

    /*
     * Стрессоустойчивость
     */
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
        $assessment->fixed_value = round($value, 2);
        $assessment->save();

        /*
         * StressResistance 7141
         */
        /* @var $max_rate MaxRate */

        try{
            $learning_area = $this->calcMaxRate($simulation, ['hero_behaviour_code'=>7141], $value);

        }catch (HeroBehaviourIsNullException $e){
            return;
        }catch (LearningGoalIsNullException $e){
            return;
        }
        $this->saveLearningArea($simulation, 9, $learning_area);


    }

    /*
     * Устойчивость к манипуляциям и давлению
     */
    public function stability() {

        /* @var $simulation Simulation */
        $simulation = $this->simulation;
        try{
            $assessment = $this->calcLearningArea($simulation, 7211);
            $learning_area = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 7211], $assessment);

        }catch (HeroBehaviourIsNullException $e){
            return;
        }catch (LearningGoalIsNullException $e){
            return;
        }
        $this->saveLearningArea($simulation, 10, $learning_area);
    }

    /*
     * Ответственность
     */
    public function responsibility(){

        /* @var $simulation Simulation */
        $simulation = $this->simulation;
        try{
            $assessment_8212 = $this->calcLearningArea($simulation, 8212);
            $assessment_8213 = $this->calcLearningArea($simulation, 8213);
            $learning_area = $this->calcMaxRate($simulation, ['learning_goal_code' => 821], ($assessment_8212+$assessment_8213));

        }catch (HeroBehaviourIsNullException $e){
            return;
        }catch (LearningGoalIsNullException $e){
            return;
        }
        $this->saveLearningArea($simulation, 12, $learning_area);
    }
    /*
     * Ориентация на результат
     */
    public function resultOrientation() {

        /* @var $simulation Simulation */
        $simulation = $this->simulation;
        try{
            $assessment = $this->calcLearningArea($simulation, 8371);
            $learning_area = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8371], $assessment);

        }catch (HeroBehaviourIsNullException $e){
            return;
        }catch (LearningGoalIsNullException $e){
            return;
        }
        $this->saveLearningArea($simulation, 14, $learning_area);

    }

    /*
     * Конструктивность
     */
    public function constructibility(){

        /* @var $simulation Simulation */
        $simulation = $this->simulation;
        try{
            $assessment = $this->calcLearningArea($simulation, 8381);
            $learning_area = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8381], $assessment);

        }catch (HeroBehaviourIsNullException $e){
            return;
        }catch (LearningGoalIsNullException $e){
            return;
        }
        $this->saveLearningArea($simulation, 15, $learning_area);

    }

    /*
     * Гибкость
     */
    public function flexibility(){

        /* @var $simulation Simulation */
        $simulation = $this->simulation;
        try{
            $assessment = $this->calcLearningArea($simulation, 8391);
            $learning_area = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8391], $assessment);

        }catch (HeroBehaviourIsNullException $e){
            return;
        }catch (LearningGoalIsNullException $e){
            return;
        }
        $this->saveLearningArea($simulation, 16, $learning_area);

    }

    /*
     * Принятие решений
     */
    public function adoptionOfDecisions(){

        /* @var $simulation Simulation */
        $simulation = $this->simulation;
        try{
            $assessment_8311 = $this->calcLearningArea($simulation, 8311);
            $assessment_8321 = $this->calcLearningArea($simulation, 8321);

            $area_8311 = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8311], ($assessment_8311-$assessment_8321));
            $weight_8311 = $this->calcWeight($simulation, 8311, $area_8311);

            $assessment_8331 = $this->calcLearningArea($simulation, 8331);
            $area_8331 = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8331], $assessment_8331);
            $weight_8331 = $this->calcWeight($simulation, 8331, $area_8331);

            $assessment_8341 = $this->calcLearningArea($simulation, 8341);
            $area_8341 = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8341], $assessment_8341);
            $weight_8341 = $this->calcWeight($simulation, 8341, $area_8341);

            $assessment_8351 = $this->calcLearningArea($simulation, 8351);
            $area_8351 = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8351], $assessment_8351);
            $weight_8351 = $this->calcWeight($simulation, 8351, $area_8351);

            $assessment_8361 = $this->calcLearningArea($simulation, 8361);
            $area_8361 = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8361], $assessment_8361);
            $weight_8361 = $this->calcWeight($simulation, 8361, $area_8361);

        }catch (HeroBehaviourIsNullException $e){
            return;
        }catch (LearningGoalIsNullException $e){
            return;
        }
        $this->saveLearningArea($simulation, 13, (round($weight_8311+$weight_8331+$weight_8341+$weight_8351+$weight_8361, 2)));
    }

    /*
     * Внимательность
     */
    public function attentiveness() {
        /* @var $simulation Simulation */
        $simulation = $this->simulation;
        try{
            $assessment = $this->calcLearningArea($simulation, 8111);
            $learning_area = $this->calcMaxRate($simulation, ['hero_behaviour_code' => 8111], $assessment);

        }catch (HeroBehaviourIsNullException $e){
            return;
        }catch (LearningGoalIsNullException $e){
            return;
        }
        $this->saveLearningArea($simulation, 11, $learning_area);
    }

    public static function getAssessment($simulation, $code) {

        /* @var $simulation Simulation */

        SimulationLearningArea::model()->findByAttributes(['sim_id'=>$simulation->id]);

    }

}