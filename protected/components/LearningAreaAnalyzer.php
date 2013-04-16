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
        $this->taskManagement();
        $this->peopleManagement();
        $this->communication();
        $this->mailManagement();
        $this->phoneManagement();
        $this->meetManagement();
        $this->imManagement();

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

        $sla = new SimulationLearningArea();
        $sla->learning_area_id = $learningArea->id;
        $sla->value = ($value < 0)?0:$value;
        $sla->sim_id = $this->simulation->id;
        $sla->save();
    }

    protected function calcCombinedSkills($learningAreaCode)
    {
        $scenario = $this->simulation->game_type;

        $total = 0;
        $maxRate = 0;
        $ids = [];

        $area = $scenario->getLearningArea(['code' => $learningAreaCode]);
        foreach ($area->learningGoals as $learningGoal) {
            /** @var SimulationLearningGoal $slg */
            $slg = SimulationLearningGoal::model()->findByAttributes([
                'sim_id' => $this->simulation->id,
                'learning_goal_id' => $learningGoal->id
            ]);

            $total += $slg->value * $slg->getReducingCoefficient();
            $ids[] = $learningGoal->id;
        }

        /** @var HeroBehaviour[] $behaviours */
        $behaviours = $scenario->getHeroBehavours(['learning_goal_id' => $ids]);
        foreach ($behaviours as $behaviour) {
            if ($behaviour->type_scale == 1) {
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
        $scenario = $this->simulation->game_type;

        $ids = [];
        $maxRate = 0;
        $total = 0;

        $behaviours = $scenario->getHeroBehavours(['code' => [1122, 1232]]);
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

        $this->saveLearningArea(1, $maxRate ? $total / $maxRate : 0);
    }

    /*
     * Управление задачами
     */
    public function taskManagement()
    {
        $value = $this->calcCombinedSkills(2);
        $this->saveLearningArea(2, $value * 100);
    }

    /*
     * Управление людьми
     */
    public function peopleManagement()
    {
        $value = $this->calcCombinedSkills(3);
        $this->saveLearningArea(3, $value * 100);
    }

    /*
     * Оптимальный выбор каналов коммуникации
     */
    public function communication()
    {
        $value = $this->calcCombinedSkills(4);
        $this->saveLearningArea(4, $value * 100);
    }

    /*
     * Эффективная работа с почтой
     */
    public function mailManagement()
    {
        $value = $this->calcCombinedSkills(5);
        $this->saveLearningArea(5, $value * 100);
    }

    /*
     * Эффективное управление звонками
     */
    public function phoneManagement()
    {
        $value = $this->calcCombinedSkills(6);
        $this->saveLearningArea(6, $value * 100);
    }

    /*
     * Эффективное управление встречами
     */
    public function meetManagement()
    {
        $value = $this->calcCombinedSkills(7);
        $this->saveLearningArea(7, $value * 100);
    }

    /*
     * Эффективная работа с IM
     */
    public function imManagement()
    {
        $value = $this->calcCombinedSkills(8);
        $this->saveLearningArea(8, $value);
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