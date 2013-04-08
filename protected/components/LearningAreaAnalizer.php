<?php


class LearningAreaAnalizer {

    public $simulation;

    public function __construct($simulation) {

        $this->simulation = $simulation;

    }

    public function run() {

    }

    public function stressResistance() {

        /* @var $simulation Simulation */
        /* @var $game_type Scenario */
        $simulation = $this->simulation;
        $game_type = $simulation->game_type;
        $point = $game_type->getHeroBehavour(['code'=>7141]);
        /* @var $max_rate MaxRate */
        $max_rate = $game_type->getMaxRate(['hero_behavour_id'=>$point->id]);
        //$point = HeroBehaviour::model()->findByAttributes(['code'=>7141]);

        /* @var $stress StressPoint[] */
        $stress = StressPoint::model()->findAllByAttributes(['sim_id'=>$this->simulation->id]);

        if(null !== $stress){
            $value = 0;
            foreach($stress as $stress_rule){
                $value += $stress_rule->stressRule->value;
            }
            $value = $value / $max_rate->rate;
        }else{
            $value = 0;
            $value = $value / $max_rate->rate;
        }

        $assessment = new AssessmentCalculation();
        $assessment->point_id = $point->id;
        $assessment->sim_id = $this->simulation->id;
        $assessment->value = round($value, 2);
        $assessment->save();

    }

}