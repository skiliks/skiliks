<?php

class AssessmentPointsTest extends CDbTestCase
{
    use UnitLoggingTrait;

    /**
     * Checks that user gains points for sent mail only once
     */
    public function testMailPointUnique()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);
        $mgr = new EventsManager();
        $logs = [];
        $template = $simulation->game_type->getMailTemplate(['code' => 'MS20']);

        $message = LibSendMs::sendMs($simulation, 'MS20');
        $this->appendNewMessage($logs, $message);
        $mgr->processLogs($simulation, $logs);
        $this->assertNotNull($template);
        $points = AssessmentPoint::model()->countByAttributes([
            'sim_id' => $simulation->id,
            'mail_id' => $template->getPrimaryKey()
        ]);

        // Send again
        $message = LibSendMs::sendMs($simulation, 'MS20');
        $this->appendNewMessage($logs, $message);

        $newPoints = AssessmentPoint::model()->countByAttributes([
            'sim_id' => $simulation->id,
            'mail_id' => $template->id
        ]);

        $this->assertEquals($points, $newPoints);
    }

    /**
     * Уточнение оценок по матрицам диалогов и писем
     * SKILISK-1679
     * @wiki: https://maprofi.atlassian.net/wiki/pages/editpage.action?pageId=11174012
     *
     * Тест проверяет что, по целям обучения по которым есть максимальная негативная оценка,
     * правильно вносится в БД корректировака позитивных оценок
     */
    public function testUpdateAggregatedAssessmentsByNegativeScaleRule()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);

        $rates = MaxRate::model()->findAll('scenario_id = 2 AND learning_goal_id IS NOT NULL AND type = :type', ['type' => MaxRate::TYPE_FAIL]);
        $learningGoalsForUpdate = [];
        $rateValues = [];
        foreach ($rates as $rate) {
            $learningGoalsForUpdate[] = $rate->learningGoal;
            $rateValues[$rate->learning_goal_id] = $rate->rate;
        }

        $learningGoalsForUpdateCodes = [];
        $sum = []; // $learningGoalsForUpdateNegativeScaleSum
        $arr = [0,0.5,1,1,1]; // ещё пару едениц - "с запасом", реально используются первые 3 цифры
        $learningGoalCoefficient = [];
        $countBehavioursInGoals = [];
        $learningGoals = [];

        foreach ($learningGoalsForUpdate as $i => $learningGoalForUpdate) {

            // learning goals codes:
            $learningGoalsForUpdateCodes[] = $learningGoalForUpdate->code;

            // init empty SUMs
            $sum[$learningGoalForUpdate->id] = 0;

            // init coefficients - I pretty sure we will have 3 first items :)
            $learningGoalCoefficient[$learningGoalForUpdate->id] = $arr[$i];

            // init empty COUNTERs
            $countBehavioursInGoals[$learningGoalForUpdate->id] = 0;

            // save learning goal to quick access by code
            $learningGoals[$learningGoalForUpdate->id] = $learningGoalForUpdate;
        }

        // protection
        $this->assertNotEmpty($learningGoalsForUpdate);

        // get positive and negative behaviours to init assessments in future {
        $learningGoalCriteria = new CDbCriteria();
        $learningGoalCriteria->addInCondition('code', $learningGoalsForUpdateCodes);
        $learningGoalsUpdate = $simulation->game_type->getLearningGoals($learningGoalCriteria);
        $negativeCriteria = new CDbCriteria();
        $negativeCriteria->addInCondition('learning_goal_id', array_map(function ($i) {return $i->getPrimaryKey();}, $learningGoalsUpdate));
        $negativeCriteria->compare('type_scale', HeroBehaviour::TYPE_NEGATIVE);
        $heroBehavioursNegative = $simulation->game_type->getHeroBehavours($negativeCriteria);
        $positiveCriteria = new CDbCriteria();
        $positiveCriteria->addInCondition('learning_goal_id', array_map(function ($i) {return $i->getPrimaryKey();}, $learningGoalsUpdate));
        $positiveCriteria->compare('type_scale', HeroBehaviour::TYPE_POSITIVE);
        $heroBehavioursPositive = $simulation->game_type->getHeroBehavours($positiveCriteria);

        // get positive and negative behaviours to init assessments in future }

        // we need this value to set right negative assessment value
        foreach ($heroBehavioursNegative as $heroBehaviour) {
            $countBehavioursInGoals[$heroBehaviour->learning_goal_id]++;
        }

        // save negative assessments for target learning goals:
        foreach ($heroBehavioursNegative as $heroBehaviour) {
            $assessmentAggregated = new AssessmentAggregated();
            $assessmentAggregated->sim_id = $simulation->id;
            $assessmentAggregated->point_id = $heroBehaviour->id;
            $assessmentAggregated->value =
                ($learningGoalCoefficient[$heroBehaviour->learning_goal_id]
                * $rateValues[$heroBehaviour->learning_goal_id])
                / $countBehavioursInGoals[$heroBehaviour->learning_goal_id];
            $assessmentAggregated->save(false);

            $sum[$heroBehaviour->learning_goal_id] += $assessmentAggregated->value;
        }

        // save some positive assessments:
        foreach ($heroBehavioursPositive as $heroBehaviour) {
            $assessmentAggregated = new AssessmentAggregated();
            $assessmentAggregated->sim_id = $simulation->id;
            $assessmentAggregated->point_id = $heroBehaviour->id;
            $assessmentAggregated->value = $heroBehaviour->scale;
            $assessmentAggregated->save(false);

            $sum[$heroBehaviour->learning_goal_id] += $assessmentAggregated->value;
        }

        // RUN REAL CODE!!! :)
        SimulationService::applyReductionFactors($simulation);

        // get data for asserts:
        $realAssessments = AssessmentAggregated::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        // asserts:

        $this->assertNotEmpty($realAssessments);
        foreach ($realAssessments as $realAssessment) {
            $learningGoalId = $realAssessment->point->learning_goal_id;
            if (HeroBehaviour::TYPE_POSITIVE == $realAssessment->point->type_scale) {
                $this->assertEquals(
                    abs(1 - $learningGoalCoefficient[$learningGoalId]), // 100% of fails => 0 points, 70% => 0.3, 25% => 0.75 etc. see SKILIKS-
                    $realAssessment->coefficient_for_fixed_value, 'Error in '.$realAssessment->point->code
                );
            }
        }
    }
}