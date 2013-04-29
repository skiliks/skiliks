<?php

class LearningGoalAnalyzer
{
    /**
     * @var Simulation
     */
    private $simulation;

    public function __construct(Simulation $sim)
    {
        $this->simulation = $sim;
    }

    public function run()
    {
        $scenario = $this->simulation->game_type;

        /** @var LearningGoal[] $learningGoals */
        $learningGoals = $scenario->getLearningGoals([]);

        $exept = ['214g1', '214g2', '214g3', '214g4', '32110', '32112', '32113',
            '32114', '3312', '3335', '341a1', '341a8', '341c1', '341c2', '371a1',
            '371a2', '371a3', '371a4', '371a5', '371b1', '371b2', '371b3', '8211'];

        $values = [];
        foreach ($this->simulation->game_type->getHeroBehavours([]) as $behaviour) {
            $values[$behaviour->id] = 0;
        }

        foreach ($this->simulation->assessment_aggregated as $row) {
            $values[$row->point_id] = $row->fixed_value;
        }

        foreach ($learningGoals as $goal) {
            if ($goal->learningArea->code > 8) { // Calc only management skills
                continue;
            }

            $totalPos = 0; $maxPos = 0;
            $totalCons = 0; $maxCons = 0;
            foreach ($goal->heroBehaviours as $behaviour) {
                // TODO: Anton decision
                // Case 1:
                // $value = isset($values[$behaviour->id]) ? $values[$behaviour->id] : 0;

                // Case 2:
                if (array_key_exists($behaviour->id, $exept)) {
                    continue;
                }
                $value = $values[$behaviour->id];

                if ($behaviour->type_scale == HeroBehaviour::TYPE_POSITIVE) { // Positive
                    $totalPos += $value;
                    $maxPos += $behaviour->scale;
                } elseif ($behaviour->type_scale == HeroBehaviour::TYPE_NEGATIVE) { //Negative
                    $totalCons += $value;
                    $maxCons += $behaviour->scale;
                }
            }

            $maxFailRate = $scenario->getMaxRate([
                'type' => MaxRate::TYPE_FAIL,
                'learning_goal_id' => $goal->id
            ]);

            $maxCons = $maxFailRate ? $maxFailRate->rate : $maxCons;

            $slg = new SimulationLearningGoal();
            $slg->sim_id = $this->simulation->id;
            $slg->learning_goal_id = $goal->id;
            $slg->value = $totalPos;
            $slg->percent = $maxPos ? substr(min($totalPos / $maxPos * 100, 100), 0, 5) : 0;
            $slg->problem = $maxCons ? substr(min($totalCons / $maxCons * 100, 100), 0, 5) : 0; // Both $totalCons and $maxCons are negative values!

            $slg->save();
        }
    }
}