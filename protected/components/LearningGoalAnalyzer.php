<?php

/**
 * Class LearningGoalAnalyzer
 */
class LearningGoalAnalyzer
{
    /**
     * @var Simulation
     */
    private $simulation;

    /**
     * @param Simulation $sim
     */
    public function __construct(Simulation $sim)
    {
        $this->simulation = $sim;
    }

    /**
     * Запуск расчета целей обучения
     */
    public function run()
    {
        $scenario = $this->simulation->game_type;

        /** @var LearningGoal[] $learningGoals */
        $learningGoalGroups = $scenario->getLearningGoalGroups([]);

        $except = HeroBehaviour::getExcludedFromAssessmentBehavioursCodes();

        $values = [];
        foreach ($this->simulation->game_type->getHeroBehavours([]) as $behaviour) {
            $values[$behaviour->id] = 0;
        }

        foreach ($this->simulation->assessment_aggregated as $row) {
            $values[$row->point_id] = $row->value;
        }

        foreach($learningGoalGroups as $learningGoalGroup) {

            $totalPosGroup = 0; $maxPosGroup = 0;
            $totalConsGroup = 0; $maxConsGroup = 0;

            /* @var $learningGoalGroup LearningGoalGroup */
            foreach ($learningGoalGroup->learningGoals as $goal) {

                if ($goal->learningArea->code > 3) { // Calc only management skills
                    continue;
                }

                $totalPos = 0; $maxPos = 0;
                $totalCons = 0; $maxCons = 0;
                foreach ($goal->heroBehaviours as $behaviour) {
                    if (in_array($behaviour->code, $except)) {
                        continue;
                    }

                    $value = $values[$behaviour->id];

                    if ($behaviour->isPositive()) {
                        $totalPos += $value;
                        $maxPos += $behaviour->scale;
                    } elseif ($behaviour->isNegative()) {
                        $totalCons += $value;
                        $maxCons += $behaviour->scale;
                    }
                }

                $maxFailRate = $scenario->getMaxRate([
                    'type' => MaxRate::TYPE_FAIL,
                    'learning_goal_id' => $goal->id
                ]);

                $maxCons = $maxFailRate ? $maxFailRate->rate : $maxCons;

                $totalPosGroup += $totalPos;
                $maxPosGroup += $maxPos;
                $totalConsGroup += $totalCons;
                $maxConsGroup += $maxCons;

                $slg = new SimulationLearningGoal();
                $slg->sim_id = $this->simulation->id;
                $slg->learning_goal_id = $goal->id;
                $slg->value = $totalPos;
                $slg->percent = $maxPos ? substr(min($totalPos / $maxPos * 100, 100), 0, 5) : 0;
                $slg->problem = $maxCons ? substr(min($totalCons / $maxCons * 100, 100), 0, 5) : 0; // Both $totalCons and $maxCons are negative values!
                $slg->total_positive = $totalPos;
                $slg->total_negative = $totalCons;
                $slg->max_positive = $maxPos;
                $slg->max_negative = $maxCons;

                $slg->save();
            }

            $simulationGoalGroup = new SimulationLearningGoalGroup();
            $simulationGoalGroup->sim_id = $this->simulation->id;
            $simulationGoalGroup->learning_goal_group_id = $learningGoalGroup->id;
            $simulationGoalGroup->value = $totalPosGroup;
            $simulationGoalGroup->percent = $maxPosGroup ? substr(min($totalPosGroup / $maxPosGroup * 100, 100), 0, 5) : 0;
            $simulationGoalGroup->problem = $maxConsGroup ? substr(min($totalConsGroup / $maxConsGroup * 100, 100), 0, 5) : 0;
            $simulationGoalGroup->total_positive = $totalPosGroup;
            $simulationGoalGroup->total_negative = $totalConsGroup;
            $simulationGoalGroup->max_positive = $maxPosGroup;
            $simulationGoalGroup->max_negative = $maxConsGroup;
            $simulationGoalGroup->save(false);
        }
    }

    /**
     * @param $problem проблемный коэффициент в процентах
     * @return int
     */
    public static function getReducingCoefficient($problem)
    {
        return (1 - $problem/100);
    }

    /**
     * Расчет положительной оценки
     * @param $totalPos набраное количество положительных баллов
     * @param $maxPos максимально возможное количество баллов
     * @return int|string
     */
    public static function calculateAssessment($totalPos, $maxPos)
    {
        return $maxPos ? substr(min($totalPos / $maxPos * 100, 100), 0, 5) : 0;
    }
}