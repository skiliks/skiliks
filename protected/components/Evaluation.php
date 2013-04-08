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
        $aggregated = AssessmentAggregated::model()->findAllByAttributes(['sim_id' => $this->simulation->id]);
        $value = 0;

        /* @var $row AssessmentAggregated */
        foreach ($aggregated as $row) {
            if($row->point->isPositive()){
                $value += $row->fixed_value;
            }
        }

        $result = new AssessmentOverall();
        $result->assessment_category_code = AssessmentCategory::MANAGEMENT_SKILLS;
        $result->sim_id = $this->simulation->id;
        $result->value = $value;

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
        $result = new AssessmentOverall();
        $result->assessment_category_code = AssessmentCategory::TIME_EFFECTIVENESS;
        $result->sim_id = $this->simulation->id;
        $result->value = 0;

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