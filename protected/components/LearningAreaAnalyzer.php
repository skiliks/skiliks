<?php

class LearningAreaAnalyzer {

    /**
     * @var Simulation
     */
    public $simulation;

    public function __construct($simulation)
    {
        $this->simulation = $simulation;
    }

    public function run()
    {
        // Management skills
        $this->followPriorities();
        $this->communicationManagement();
        $this->peopleManagement();

        // Personal scale
        $this->stressResistance();
        $this->stability();
        $this->responsibility();
        $this->adoptionOfDecisions();
        $this->resultOrientation();
        $this->constructibility();
        $this->flexibility();
        $this->attentiveness();
    }

    protected function calcLearningArea($code)
    {
        $scenario = $this->simulation->game_type;
        $point = $scenario->getHeroBehaviour(['code' => $code]);

        if (null === $point) {
            throw new HeroBehaviourIsNullException(" Not Found {$code} ");
        }

        $aggregated = AssessmentAggregated::model()->findByAttributes([
            'sim_id' => $this->simulation->id,
            'point_id' => $point->id
        ]);

        return $aggregated ? $aggregated->value : 0;
    }

    protected function calcMaxRate($code, $assessment)
    {
        $scenario = $this->simulation->game_type;

        if (!empty($code["learning_goal_code"])) {
            $point = $scenario->getLearningGoal(['code' => $code["learning_goal_code"]]);
            if (empty($point)) {
                return 0;
            }
            $condition = ['learning_goal_id' => $point->id];
        } elseif (!empty($code["hero_behaviour_code"])) {
            $point = $scenario->getHeroBehaviour(['code' => $code["hero_behaviour_code"]]);
            if (empty($point)) {
                return 0;
            }
            $condition = ['hero_behaviour_id' => $point->id];
        } else {
            throw new Exception("Parameter 'learning_goal_code' or 'hero_behaviour_code' is not found");
        }

        $maxRate = $scenario->getMaxRate($condition);

        if (empty($maxRate) && empty($point->scale)) {
            return 0;
        } else {
            return $assessment / ($maxRate ? $maxRate->rate : $point->scale) * 100;
        }
    }

    protected function calcWeight($code, $value)
    {
        $scenario = $this->simulation->game_type;

        $point = $scenario->getHeroBehaviour(['code' => $code]);
        if (null === $point) {
            throw new HeroBehaviourIsNullException(" Not Found {$code} ");
        }

        $weight = $scenario->getWeight(['hero_behaviour_id' => $point->id]);
        if (null === $weight) {
            throw new HeroBehaviourIsNullException(" Not Found {$code} ");
        }

        return $weight->value * $value;
    }

    protected function saveLearningArea($code, $value)
    {
        $learningArea = $this->simulation->game_type->getLearningArea(['code' => $code]);

        if ($learningArea) {
            $sla = new SimulationLearningArea();
            $sla->learning_area_id = $learningArea->id;
            $sla->value = substr(max(min($value, 100), 0), 0, 10);
            $sla->sim_id = $this->simulation->id;
            $sla->save(false);
        }
    }

    protected function calcCombinedSkillsByBehaviours(array $behaviourCodes)
    {
        $scenario = $this->simulation->game_type;

        $ids = [];
        $maxRate = 0;
        $total = 0;

        $behaviours = $scenario->getHeroBehavours(['code' => $behaviourCodes]);
        foreach ($behaviours as $behaviour) {
            $ids[] = $behaviour->id;
            $maxRate += $behaviour->scale;
        }

        $aggregated = AssessmentAggregated::model()->findAllByAttributes([
            'sim_id' => $this->simulation->id,
            'point_id' => $ids
        ]);

        foreach ($aggregated as $item) {
            $total += $item->fixed_value;
        }

        return $maxRate ? $total / $maxRate : 0;
    }

    protected function calcCombinedSkillsByGoalGroup($learningAreaCode)
    {
        $scenario = $this->simulation->game_type;

        $total = 0;
        $maxRate = 0;
        $ids = [];

        $except = HeroBehaviour::getExcludedFromAssessmentBehavioursCodes();

        $area = $scenario->getLearningArea(['code' => $learningAreaCode]);
        if ($area) {
            foreach ($area->learningGoalGroups as $learningGoalGroup) {


                /** @var SimulationLearningGoalGroup $slg */
                $slg = SimulationLearningGoalGroup::model()->findByAttributes([
                    'sim_id' => $this->simulation->id,
                    'learning_goal_group_id' => $learningGoalGroup->id
                ]);

                if ($slg) {
                    $total += $slg->value * $slg->getReducingCoefficient();
                    $slg->coefficient = $slg->getReducingCoefficient();
                    $slg->update();
                }

                /* @var $learningGoalGroup LearningGoalGroup */
                /* @var $goal LearningGoal */
                foreach($learningGoalGroup->learningGoals as $goal){
                    $ids[] = $goal->id;
                }
            }
        }

        /** @var HeroBehaviour[] $behaviours */
        $behaviours = $scenario->getHeroBehavours(['learning_goal_id' => $ids]);
        foreach ($behaviours as $behaviour) {
            // TODO: Anton decision
            // Remove out second condition
            if ($behaviour->type_scale == 1 && !in_array($behaviour->code, $except)) {
                $maxRate += $behaviour->scale;
            }
        }

        return $maxRate ? $total / $maxRate : 0;
    }

    protected function calcCombinedSkillsByGoalGroupPriority($learningAreaCode)
    {
        $scenario = $this->simulation->game_type;

        $total = 0;
        $maxRate = 0;
        $ids = [];

        $group_1_4_value = 0;
        $group_1_4_negative = 0;
        $group_1_4_max_negative = 0;
        $group_1_4 = null;
        //$except = HeroBehaviour::getExcludedFromAssessmentBehavioursCodes();

        $area = $scenario->getLearningArea(['code' => $learningAreaCode]);
        if ($area) {
            /* @var $area LearningArea */
            foreach ($area->learningGoalGroups as $learningGoalGroup) {
                /** @var SimulationLearningGoalGroup $slg */
                $slg = SimulationLearningGoalGroup::model()->findByAttributes([
                    'sim_id' => $this->simulation->id,
                    'learning_goal_group_id' => $learningGoalGroup->id
                ]);

                /* @var $learningGoalGroup LearningGoalGroup */
                /* @var $goal LearningGoal */
                foreach($learningGoalGroup->learningGoals as $goal){
                    $ids[] = $goal->id;
                }

                if($learningGoalGroup->code === '1_4') {
                    $group_1_4_value += $slg->value;
                    $group_1_4 = $slg;
                    // 214d {
                    $goal214d = LearningGoal::model()->findByAttributes([
                        'scenario_id'           => $scenario->id,
                        'code' => '214d'
                    ]);

                    $fail214d = maxRate::model()->findByAttributes([
                        'scenario_id'      => $scenario->id,
                        'learning_goal_id' => $goal214d->id
                    ]);

                    $group_1_4_max_negative += $fail214d->rate;
                    // 214d }

                    // 214d5 {
                    $behaviour214d5 = $scenario->getHeroBehaviour(['code' => '214d5']);

                    $assessmentAggregated214d5 = AssessmentAggregated::model()->findByAttributes([
                        'sim_id'   => $this->simulation->id,
                        'point_id' => $behaviour214d5->id,
                    ]);

                    $group_1_4_negative += (empty($assessmentAggregated214d5)) ? 0 : $assessmentAggregated214d5->value;
                    // 214d5 }

                    // 214d6 {
                    $behaviour214d6 = $scenario->getHeroBehaviour(['code' => '214d6']);

                    $assessmentAggregated214d6 = AssessmentAggregated::model()->findByAttributes([
                        'sim_id'   => $this->simulation->id,
                        'point_id' => $behaviour214d6->id,
                    ]);

                    $group_1_4_negative += (empty($assessmentAggregated214d6)) ? 0 : $assessmentAggregated214d6->value;
                    // 214d6 }

                    // 214d8 {
                    $behaviour214d8 = $scenario->getHeroBehaviour(['code' => '214d8']);

                    $assessmentAggregated214d8 = AssessmentAggregated::model()->findByAttributes([
                        'sim_id'   => $this->simulation->id,
                        'point_id' => $behaviour214d8->id,
                    ]);

                    $group_1_4_negative += (empty($assessmentAggregated214d8)) ? 0 : $assessmentAggregated214d8->value;
                    // 214d8 }

                    continue;
                }
                if($learningGoalGroup->code === '1_5') {
                    $group_1_4_negative += $slg->total_negative;

                    // 214g {
                    $goal214g = LearningGoal::model()->findByAttributes([
                        'scenario_id'           => $scenario->id,
                        'code' => '214g'
                    ]);

                    $fail214g = maxRate::model()->findByAttributes([
                        'scenario_id'      => $scenario->id,
                        'learning_goal_id' => $goal214g->id
                    ]);

                    $group_1_4_max_negative += $fail214g->rate;
                    // 214g }

                    // 214g0 {
                    $behaviour214g0 = $scenario->getHeroBehaviour(['code' => '214g0']);

                    $assessmentAggregated214g0 = AssessmentAggregated::model()->findByAttributes([
                        'sim_id'   => $this->simulation->id,
                        'point_id' => $behaviour214g0->id,
                    ]);

                    $group_1_4_negative += (empty($assessmentAggregated214g0)) ? 0 : $assessmentAggregated214g0->value;
                    // 214g0 }

                    // 214g1 {
                    $behaviour214g1 = $scenario->getHeroBehaviour(['code' => '214g1']);

                    $assessmentAggregated214g1 = AssessmentAggregated::model()->findByAttributes([
                        'sim_id'   => $this->simulation->id,
                        'point_id' => $behaviour214g1->id,
                    ]);

                    $group_1_4_negative += (empty($assessmentAggregated214g1)) ? 0 : $assessmentAggregated214g1->value;
                    // 214g1 }

                    continue;
                }

                if ($slg) {
                    $total += $slg->value * $slg->getReducingCoefficient();
                    $slg->coefficient = $slg->getReducingCoefficient();
                    $slg->update();
                }
            }
        }

        $k = LearningGoalAnalyzer::getReducingCoefficient(
            LearningGoalAnalyzer::calculateAssessment(
                $group_1_4_negative,
                $group_1_4_max_negative
            )
        );
        $total += $group_1_4_value * $k;
        $group_1_4->coefficient = $k;
        $group_1_4->update();
        /** @var HeroBehaviour[] $behaviours */
        $behaviours = $scenario->getHeroBehavours(['learning_goal_id' => $ids]);
        foreach ($behaviours as $behaviour) {
            // TODO: Anton decision
            // Remove out second condition

            if ($behaviour->type_scale == HeroBehaviour::TYPE_ID_POSITIVE/* && !in_array($behaviour->code, $except)*/) {
                $maxRate += $behaviour->scale;
            }
        }

        return $maxRate ? $total / $maxRate : 0;
    }


    /*
     * Следование приоритетам
     */
    public function followPriorities()
    {
        $value = $this->calcCombinedSkillsByGoalGroupPriority(1);
        $this->saveLearningArea(1, $value * 100);
    }

    /*
     * Управление задачами
     */
    public function communicationManagement()
    {
        $value = $this->calcCombinedSkillsByGoalGroup(3);
        $this->saveLearningArea(3, $value * 100);
    }

    /*
     * Управление людьми
     */
    public function peopleManagement()
    {
        $value = $this->calcCombinedSkillsByGoalGroup(2);
        $this->saveLearningArea(2, $value * 100);
    }

    /*
     * Стрессоустойчивость
     */
    public function stressResistance()
    {
        try {
            $assessment = $this->calcLearningArea(7141);
            $learningArea = $this->calcMaxRate(['hero_behaviour_code'=>7141], $assessment);
        } catch (HeroBehaviourIsNullException $e) {
            return;
        } catch (LearningGoalIsNullException $e) {
            return;
        }

        $this->saveLearningArea(9, $learningArea);
    }

    /*
     * Устойчивость к манипуляциям и давлению
     */
    public function stability()
    {
        try {
            $assessment = $this->calcLearningArea(7211);
            $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 7211], $assessment);
        } catch (HeroBehaviourIsNullException $e) {
            return;
        } catch (LearningGoalIsNullException $e) {
            return;
        }

        $this->saveLearningArea(10, $learningArea);
    }

    /*
     * Ответственность
     */
    public function responsibility()
    {
        try {
            $assessment_8212 = $this->calcLearningArea(8212);
            $assessment_8213 = $this->calcLearningArea(8213);
            $learningArea = $this->calcMaxRate(['learning_goal_code' => 821], $assessment_8212 + $assessment_8213);
        } catch (HeroBehaviourIsNullException $e) {
            return;
        } catch (LearningGoalIsNullException $e) {
            return;
        }

        $this->saveLearningArea(12, $learningArea);
    }

    /*
     * Ориентация на результат
     */
    public function resultOrientation()
    {
        try {
            $assessment = $this->calcLearningArea(8371);
            $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 8371], $assessment);
        } catch (HeroBehaviourIsNullException $e) {
            return;
        } catch (LearningGoalIsNullException $e) {
            return;
        }

        $this->saveLearningArea(14, $learningArea);
    }

    /*
     * Конструктивность
     */
    public function constructibility()
    {
        try {
            $assessment = $this->calcLearningArea(8381);
            $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 8381], $assessment);
        } catch (HeroBehaviourIsNullException $e) {
            return;
        } catch (LearningGoalIsNullException $e) {
            return;
        }

        $this->saveLearningArea(15, $learningArea);
    }

    /*
     * Гибкость
     */
    public function flexibility()
    {
        try {
            $assessment = $this->calcLearningArea(8391);
            $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 8391], $assessment);
        } catch (HeroBehaviourIsNullException $e) {
            return;
        } catch (LearningGoalIsNullException $e) {
            return;
        }

        $this->saveLearningArea(16, $learningArea);
    }

    /*
     * Принятие решений
     */
    public function adoptionOfDecisions()
    {
        try {
            $assessment_8311 = $this->calcLearningArea(8311);
            $assessment_8321 = $this->calcLearningArea(8321);

            $area_8311 = $this->calcMaxRate(['hero_behaviour_code' => 8311], $assessment_8311 - $assessment_8321);
            $weight_8311 = $this->calcWeight(8311, $area_8311);

            $assessment_8331 = $this->calcLearningArea(8331);
            $area_8331 = $this->calcMaxRate(['hero_behaviour_code' => 8331], $assessment_8331);
            $weight_8331 = $this->calcWeight(8331, $area_8331);

            $assessment_8341 = $this->calcLearningArea(8341);
            $area_8341 = $this->calcMaxRate(['hero_behaviour_code' => 8341], $assessment_8341);
            $weight_8341 = $this->calcWeight(8341, $area_8341);

            $assessment_8351 = $this->calcLearningArea(8351);
            $area_8351 = $this->calcMaxRate(['hero_behaviour_code' => 8351], $assessment_8351);
            $weight_8351 = $this->calcWeight(8351, $area_8351);

            $assessment_8361 = $this->calcLearningArea(8361);
            $area_8361 = $this->calcMaxRate(['hero_behaviour_code' => 8361], $assessment_8361);
            $weight_8361 = $this->calcWeight(8361, $area_8361);
        } catch (HeroBehaviourIsNullException $e) {
            return;
        } catch (LearningGoalIsNullException $e) {
            return;
        }

        $this->saveLearningArea(13, $weight_8311 + $weight_8331 + $weight_8341 + $weight_8351 + $weight_8361);
    }

    /*
     * Внимательность
     */
    public function attentiveness()
    {
        try {
            $assessment = $this->calcLearningArea(8111);
            $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 8111], $assessment);
        } catch (HeroBehaviourIsNullException $e) {
            return;
        } catch (LearningGoalIsNullException $e) {
            return;
        }

        $this->saveLearningArea(11, $learningArea);
    }
}