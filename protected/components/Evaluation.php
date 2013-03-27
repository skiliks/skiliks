<?php

class Evaluation {

    protected $simId;

    public function __construct($simId) {

        $this->simId = $simId;



    }

    public function checkOverallManagerRating() {

        $simulation = Simulation::model()->findByAttributes(['id'=>$this->simId]);
        $value = 0.5 * $simulation->managerial_skills
               + 0.3 * $simulation->managerial_productivity
               + 0.2 * $simulation->time_management_effectiveness;

        $simulation->overall_manager_rating = round($value, 2);
        $simulation->update();
    }

    public function checkManagerialSkills() {


        $assessment_aggregated = AssessmentAggregated::model()->findAllByAttributes(['sim_id' => $this->simId]);
        $value = 0;
        if(null !== $assessment_aggregated) {
            foreach($assessment_aggregated as $assessment) {
                $value += $assessment->fixed_value;
            }
        }
        $simulation = Simulation::model()->findByAttributes(['id'=>$this->simId]);
        $simulation->managerial_skills = round($value, 2);
        $simulation->update();

    }

    public function checkManagerialProductivity() {

        $assessment_rule = SimulationAssessmentRule::model()->findAllByAttributes(['sim_id' => $this->simId]);
        $value = 0;
        if(null !== $assessment_rule) {
            foreach($assessment_rule as $assessment) {
                $value += $assessment->assessmentRule->value;
            }
        }
        $simulation = Simulation::model()->findByAttributes(['id'=>$this->simId]);
        $simulation->managerial_productivity = round($value, 2);
        $simulation->update();

    }

    public function checkTimeManagementEffectiveness() {

        $simulation = Simulation::model()->findByAttributes(['id'=>$this->simId]);
        $simulation->time_management_effectiveness = 0;
        $simulation->update();

    }

    public function run(){
        $this->checkManagerialSkills();
        $this->checkManagerialProductivity();
        $this->checkTimeManagementEffectiveness();
        $this->checkOverallManagerRating();
    }


}