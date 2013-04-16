<?php

class Evaluation {

    /**
     * @var Simulation
     */
    public $simulation;

    public function __construct($simulation) {
        $this->simulation = $simulation;
    }

    public function checkOverallManagerRating()
    {
        $value = 0;
        $rates = AssessmentOverall::model()->findAllByAttributes([
            'sim_id' => $this->simulation->id,
        ]);

        /** @var AssessmentOverall[] $rates */
        foreach ($rates as $rate) {
            if ($rate->assessment_category_code == AssessmentCategory::OVERALL) {
                throw new DomainException('Shit happened!');
            }

            $weight = $this->simulation->game_type->getWeight([
                'assessment_category_code' => $rate->assessment_category_code
            ]);

            if ($weight === null) {
                $value = 0;
            } else {
                $value += $weight->value * $rate->value;
            }
        }

        $overall = new AssessmentOverall();
        $overall->sim_id = $this->simulation->id;
        $overall->assessment_category_code = AssessmentCategory::OVERALL;
        $overall->value = $value;

        $overall->save();
    }

    public function checkManagerialSkills()
    {
        $scenario = $this->simulation->game_type;

        $total = 0;
        $maxRate = 0;

        foreach ($this->simulation->learning_goal as $goalPoint) {
            if ($goalPoint->learningGoal->learningArea->code <= 8) {
                $total += $goalPoint->value * $goalPoint->getReducingCoefficient();
            }
        }

        /** @var HeroBehaviour[] $behaviours */
        $behaviours = $scenario->getHeroBehavours(['type_scale' => 1]);
        foreach ($behaviours as $behaviour) {
            $maxRate += $behaviour->scale;
        }

        $result = new AssessmentOverall();
        $result->assessment_category_code = AssessmentCategory::MANAGEMENT_SKILLS;
        $result->sim_id = $this->simulation->id;
        $result->value = substr($maxRate ? $total / $maxRate * 100 : 0, 0, 10);

        $result->save();
    }

    public function checkManagerialProductivity()
    {
        $value = 0;

        /** @var PerformanceAggregated[] $aggregation */
        $aggregation = PerformanceAggregated::model()->findAllByAttributes([
            'sim_id' => $this->simulation->id
        ]);

        foreach ($aggregation as $aggregationCategory) {
            $weight = $this->simulation->game_type->getWeight([
                'performance_rule_category_id' => $aggregationCategory->category_id
            ]);

            $value += $weight->value * $aggregationCategory->value;
        }

        $result = new AssessmentOverall();
        $result->assessment_category_code = AssessmentCategory::PRODUCTIVITY;
        $result->sim_id = $this->simulation->id;
        $result->value = $value;

        $result->save();
    }

    public function checkTimeManagementEffectiveness()
    {
        $timeManagementEfficiency = TimeManagementAggregated::model()->findByAttributes([
            'sim_id' => $this->simulation->id,
            'slug'   => TimeManagementAggregated::SLUG_EFFICIENCY
        ]);

        $result = new AssessmentOverall();
        $result->assessment_category_code = AssessmentCategory::TIME_EFFECTIVENESS;
        $result->sim_id = $this->simulation->id;
        $result->value = (null === $timeManagementEfficiency) ? 0 : $timeManagementEfficiency->value;

        $result->save();
    }

    public function run()
    {
        $this->checkManagerialSkills();
        $this->checkManagerialProductivity();
        $this->checkTimeManagementEffectiveness();
        $this->checkOverallManagerRating();
    }


}