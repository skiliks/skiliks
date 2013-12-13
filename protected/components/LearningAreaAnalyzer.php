<?php

/**
 * Class LearningAreaAnalyzer
 */
class LearningAreaAnalyzer {

    /**
     * @var Simulation
     */
    public $simulation;

    /**
     * @param Simulation $simulation
     */
    public function __construct(Simulation $simulation)
    {
        $this->simulation = $simulation;
    }

    /**
     * Запуск расчета областей
     */
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

    /**
     * Расчет области обучения по коду
     * @param $code
     * @return int
     */
    protected function calcLearningArea($code)
    {
        $scenario = $this->simulation->game_type;
        $point = $scenario->getHeroBehaviour(['code' => $code]);

        $aggregated = AssessmentAggregated::model()->findByAttributes([
            'sim_id' => $this->simulation->id,
            'point_id' => $point->id
        ]);

        return $aggregated ? $aggregated->value : 0;
    }

    /**
     * Расчет максрейта
     * @param $code hero_behaviour_code или learning_goal_code
     * @param $assessment агрегированая оценка по повидению
     * @return float|int
     * @throws Exception
     */
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

    /**
     * Расчет весов
     * @param $code
     * @param $value
     * @return float
     */
    protected function calcWeight($code, $value)
    {
        $scenario = $this->simulation->game_type;

        $point = $scenario->getHeroBehaviour(['code' => $code]);

        $weight = $scenario->getWeight(['hero_behaviour_id' => $point->id]);

        return $weight->value * $value;
    }

    /**
     * Сохраняет область обучения
     * @param string $code код области
     * @param string $value процены
     */
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

    /**
     * Комбинирование оценок по целям обучений
     * @param string $learningAreaCode код обучения
     * @return array
     */
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

        return ['maxRate'=>$maxRate, 'total'=>$total]; //$maxRate ? $total / $maxRate : 0;
    }

    /**
     * Комбинирование оценок по целям обучений с учётом приоритетов
     * @param string $learningAreaCode
     * @return array maxRate - максрейт для группы целей и total - позитивная оценка по этой группе
     */
    protected function calcCombinedSkillsByGoalGroupPriority($learningAreaCode)
    {
        $scenario = $this->simulation->game_type;

        $total = 0;
        $maxRate = 0;
        $ids = [];

        $group_1_3_value = 0;
        $group_1_3_negative = 0;
        $group_1_3_max_negative = 0;
        $group_1_3 = null;
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

                if($learningGoalGroup->code === '1_3') {
                    $group_1_3_value += $slg->value;
                    $group_1_3 = $slg;

                    $group_1_3_negative += $slg->total_negative;
                    $group_1_3_max_negative += $slg->max_negative;
                    continue;
                }
                if($learningGoalGroup->code === '1_4') {
                    $group_1_3_negative += $slg->total_negative;
                    $group_1_3_max_negative += $slg->max_negative;

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
                $group_1_3_negative,
                $group_1_3_max_negative
            )
        );
        $total += $group_1_3_value * $k;

        if (isset($group_1_3)) {
            $group_1_3->coefficient = $k;
            $group_1_3->update();
        }

        /** @var HeroBehaviour[] $behaviours */
        $behaviours = $scenario->getHeroBehavours(['learning_goal_id' => $ids]);
        foreach ($behaviours as $behaviour) {
            // TODO: Anton decision
            // Remove out second condition

            if ($behaviour->type_scale == HeroBehaviour::TYPE_ID_POSITIVE/* && !in_array($behaviour->code, $except)*/) {
                $maxRate += $behaviour->scale;
            }
        }

        return ['maxRate'=>$maxRate, 'total'=>$total];//$maxRate ? $total / $maxRate : 0;
    }


    /**
     * Следование приоритетам
     */
    public function followPriorities()
    {
        $value = $this->calcCombinedSkillsByGoalGroupPriority(1);
        $this->saveLearningAreaByGoal(1, $value);
    }

    /**
     * Управление задачами
     */
    public function communicationManagement()
    {
        $value = $this->calcCombinedSkillsByGoalGroup(3);
        $this->saveLearningAreaByGoal(3, $value);
    }

    /**
     * Управление людьми
     */
    public function peopleManagement()
    {
        $value = $this->calcCombinedSkillsByGoalGroup(2);
        $this->saveLearningAreaByGoal(2, $value);
    }

    /**
     * Стрессоустойчивость
     */
    public function stressResistance()
    {
        $assessment = $this->calcLearningArea(7141);
        $learningArea = $this->calcMaxRate(['hero_behaviour_code'=>7141], $assessment);

        $this->saveLearningArea(9, $learningArea);
    }

    /**
     * Устойчивость к манипуляциям и давлению
     */
    public function stability()
    {
        $assessment = $this->calcLearningArea(7211);
        $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 7211], $assessment);

        $this->saveLearningArea(10, $learningArea);
    }

    /**
     * Ответственность
     */
    public function responsibility()
    {
        $assessment_8212 = $this->calcLearningArea(8212);
        $assessment_8213 = $this->calcLearningArea(8213);
        $learningArea = $this->calcMaxRate(['learning_goal_code' => 821], $assessment_8212 + $assessment_8213);

        $this->saveLearningArea(12, $learningArea);
    }

    /**
     * Ориентация на результат
     */
    public function resultOrientation()
    {
        $assessment = $this->calcLearningArea(8371);
        $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 8371], $assessment);

        $this->saveLearningArea(14, $learningArea);
    }

    /**
     * Конструктивность
     */
    public function constructibility()
    {
        $assessment = $this->calcLearningArea(8381);
        $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 8381], $assessment);

        $this->saveLearningArea(15, $learningArea);
    }

    /**
     * Гибкость
     */
    public function flexibility()
    {
        $assessment = $this->calcLearningArea(8391);
        $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 8391], $assessment);

        $this->saveLearningArea(16, $learningArea);
    }

    /**
     * Принятие решений
     */
    public function adoptionOfDecisions()
    {
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

        $this->saveLearningArea(13, $weight_8311 + $weight_8331 + $weight_8341 + $weight_8351 + $weight_8361);
    }

    /**
     * Внимательность
     */
    public function attentiveness()
    {
        $assessment = $this->calcLearningArea(8111);
        $learningArea = $this->calcMaxRate(['hero_behaviour_code' => 8111], $assessment);

        $this->saveLearningArea(11, $learningArea);
    }

    /**
     * Сохранение области обучений
     * @param string $code код обалсти
     * @param array $params  maxRate и total
     */
    protected function saveLearningAreaByGoal($code, $params)
    {
        $learningArea = $this->simulation->game_type->getLearningArea(['code' => $code]);
        $value = ($params['maxRate'] ? $params['total'] / $params['maxRate'] : 0)*100;
        if ($learningArea) {
            $sla = new SimulationLearningArea();
            $sla->learning_area_id = $learningArea->id;
            $sla->value = substr(max(min($value, 100), 0), 0, 10);
            $sla->score = $params['total'];
            $sla->sim_id = $this->simulation->id;
            $sla->save(false);
        }
    }
}