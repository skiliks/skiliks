<?php
/**
 * Пересчитывает оценку у симуляции по email и ID симуляции
 */
class CalculateTheEstimateCommand extends CConsoleCommand {

    public function actionIndex($email, $simId)
    {
        /** @var  $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        if(null === $simulation){
            throw new Exception("Simulation by id = {$simId} not found.");
        }
        /* @var $profile YumProfile */
        $profile = YumProfile::model()->findByAttributes(['email'=>$email]);
        if(null === $profile){
            throw new Exception("Profile by email = {$email} not found.");
        }

        if($profile->user_id !== $simulation->user_id){
            throw new Exception("This simulation does not belong to this user.");
        }

        echo 'Clean tables: ... ';
        LogActivityActionAgregated::model()->deleteAllByAttributes(['sim_id' => $simId]);

        TimeManagementAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentCalculation::model()->deleteAllByAttributes(['sim_id' => $simId]);
        DayPlanLog::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogActivityActionAgregated214d::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentPlaningPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationExcelPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        PerformancePoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        PerformanceAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        StressPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationLearningGoal::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationLearningArea::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentOverall::model()->deleteAllByAttributes(['sim_id' => $simId]);

        echo "done!\nRecalculating: ... ";
        SimulationService::simulationStop($simulation);

        echo "done for email {$email} and simId {$simId}.\r\n";
    }

}