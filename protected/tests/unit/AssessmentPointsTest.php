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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        $mgr = new EventsManager();
        $logs = [];
        $template = MailTemplate::model()->byCode('MS20')->find();

        $message = LibSendMs::sendMs($simulation, 'MS20');
        $this->appendNewMessage($logs, $message);
        $mgr->processLogs($simulation, $logs);

        $points = AssessmentPoint::model()->countByAttributes([
            'sim_id' => $simulation->id,
            'mail_id' => $template->id
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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $learningGoalsForUpdate = LearningGoal::model()->findAll(' max_negative_value IS NOT NULL ');

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
            $sum[$learningGoalForUpdate->code] = 0;

            // init coefficients - I pretty sure we will have 3 first items :)
            $learningGoalCoefficient[$learningGoalForUpdate->code] = $arr[$i];

            // init empty COUNTERs
            $countBehavioursInGoals[$learningGoalForUpdate->code] = 0;

            // save learning goal to quick access by code
            $learningGoals[$learningGoalForUpdate->code] = $learningGoalForUpdate;
        }

        // protection
        $this->assertNotEmpty($learningGoalsForUpdate);

        // get positive and negative behaviours to init assessments in future {
        $heroBehavioursNegative = HeroBehaviour::model()->findAll(sprintf(
            'learning_goal_code IN (%s) AND type_scale = %s',
            implode(',', $learningGoalsForUpdateCodes),
            HeroBehaviour::TYPE_NEGATIVE
        ));

        $heroBehavioursPositive = HeroBehaviour::model()->findAll(sprintf(
            'learning_goal_code IN (%s) AND type_scale = %s',
            implode(',', $learningGoalsForUpdateCodes),
            HeroBehaviour::TYPE_POSITIVE
        ));
        // get positive and negative behaviours to init assessments in future }

        // we need this value to set right negative assessment value
        foreach ($heroBehavioursNegative as $heroBehaviour) {
            $countBehavioursInGoals[$heroBehaviour->learning_goal_code]++;
        }

        // save negative assessments for target learning goals:
        foreach ($heroBehavioursNegative as $heroBehaviour) {
            $assessmentAggregated = new AssessmentAggregated();
            $assessmentAggregated->sim_id = $simulation->id;
            $assessmentAggregated->point_id = $heroBehaviour->id;
            $assessmentAggregated->value =
                ($learningGoalCoefficient[$heroBehaviour->learning_goal_code]
                * $learningGoals[$heroBehaviour->learning_goal_code]->max_negative_value)
                / $countBehavioursInGoals[$heroBehaviour->learning_goal_code];
            $assessmentAggregated->save(false);

            $sum[$heroBehaviour->learning_goal_code] += $assessmentAggregated->value;
        }

        // save some positive assessments:
        foreach ($heroBehavioursPositive as $heroBehaviour) {
            $assessmentAggregated = new AssessmentAggregated();
            $assessmentAggregated->sim_id = $simulation->id;
            $assessmentAggregated->point_id = $heroBehaviour->id;
            $assessmentAggregated->value = $heroBehaviour->scale;
            $assessmentAggregated->save(false);

            $sum[$heroBehaviour->learning_goal_code] += $assessmentAggregated->value;
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
            $code = $realAssessment->point->learning_goal_code;
            if (HeroBehaviour::TYPE_POSITIVE == $realAssessment->point->type_scale) {
                $this->assertEquals(
                    abs(1 - $learningGoalCoefficient[$code]), // 100% of fails => 0 points, 70% => 0.3, 25% => 0.75 etc. see SKILIKS-
                    $realAssessment->coefficient_for_fixed_value, 'Error in '.$realAssessment->point->code
                );
            }
        }
    }
}