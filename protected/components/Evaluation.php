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
        $overall->value = substr($value, 0, 10);

        $overall->save();
    }

    public function checkManagerialSkills()
    {
        $followPriorities = $this->simulation->game_type->getLearningArea(['code' => 1]);
        /* @var $simFollowPriorities SimulationLearningArea */
        $simFollowPriorities = SimulationLearningArea::model()->findByAttributes([
            'sim_id'           => $this->simulation->id,
            'learning_area_id' => (empty($followPriorities)) ? null : $followPriorities->id
        ]);
        if(null === $simFollowPriorities) {
            $simFollowPrioritiesValue = 0;
        } else {
            $simFollowPrioritiesValue = $simFollowPriorities->score;
        }

        $communicationManagement = $this->simulation->game_type->getLearningArea(['code' => 3]);
        /* @var $simCommunicationManagement SimulationLearningArea */
        $simCommunicationManagement = SimulationLearningArea::model()->findByAttributes([
            'sim_id'           => $this->simulation->id,
            'learning_area_id' => (empty($communicationManagement)) ? null : $communicationManagement->id
        ]);
        if(null === $simCommunicationManagement) {
            $simCommunicationManagementValue = 0;
        } else {
            $simCommunicationManagementValue = $simCommunicationManagement->score;
        }

        $peopleManagement = $this->simulation->game_type->getLearningArea(['code' => 2]);
        /* @var $simPeopleManagement SimulationLearningArea */
        $simPeopleManagement = SimulationLearningArea::model()->findByAttributes([
            'sim_id'           => $this->simulation->id,
            'learning_area_id' => (empty($peopleManagement)) ? null : $peopleManagement->id
        ]);
        if(null === $simPeopleManagement) {
            $simPeopleManagementValue = 0;
        } else {
            $simPeopleManagementValue = $simPeopleManagement->score;
        }

        $result = new AssessmentOverall();
        $result->assessment_category_code = AssessmentCategory::MANAGEMENT_SKILLS;
        $result->sim_id = $this->simulation->id;
        $result->value = LearningGoalAnalyzer::calculateAssessment(
            $simPeopleManagementValue + $simFollowPrioritiesValue + $simCommunicationManagementValue,
            100
        );

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

            $value += $weight->value * $aggregationCategory->percent;
        }

        $result = new AssessmentOverall();
        $result->assessment_category_code = AssessmentCategory::PRODUCTIVITY;
        $result->sim_id = $this->simulation->id;
        $value = ($value > 100)?round($value):$value;
        $result->value = substr($value, 0, 10);

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
        $result->value = (null === $timeManagementEfficiency) ? 0 : substr($timeManagementEfficiency->value, 0, 10);

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