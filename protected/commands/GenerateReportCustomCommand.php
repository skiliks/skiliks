<?php
use application\components\Logging\LogTableList as LogTableList;
/**
 * Created by Vladimir Boyko Skilix.
 */

/**
 *
 * Выбирает полный лог всех юзеров из базы
 */
class GenerateReportCustomCommand extends CConsoleCommand
{
    public function actionIndex() // 7 days
    {


        $assessment_version = 'v1';
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        /* @var Simulation[] $simulations */
        $simulations = Simulation::model()->findAll("scenario_id = {$scenario->id} and assessment_version = '{$assessment_version}' and end is not null");
        $data_simulations = [];
        $categories = AssessmentOverall::model()->findAllByAttributes([
            'assessment_category_code' => AssessmentCategory::PERCENTILE,
        ]);
        foreach($categories as $category) {
            $categories_percentile[$category->sim_id] = $category->value;
        }
        foreach($simulations as $simulation) {
            //if(count($data_simulations) > 5 ) break;

            if(isset($categories_percentile[$simulation->id]) && $categories_percentile[$simulation->id] != 0) {
                $data_simulations[$simulation->id] = $simulation;

            }
        }
        $profile = YumProfile::model()->findByAttributes(['email' => 'e.sarnova@august-bel.by']);
        $simulations = Simulation::model()->findAll("user_id = {$profile->user_id} and scenario_id = {$scenario->id} and assessment_version = '{$assessment_version}'  and end is not null");
        foreach($simulations as $simulation) {
            $data_simulations[$simulation->id] = $simulation;
        }
        echo "Calc ".count($data_simulations)." \r\n";
        if(!empty($data_simulations)) {
            $logTableList = new LogTableList();
            foreach($data_simulations as $data_simulation) {
                $logTableList->setSimulation($data_simulation);
                $logTableList->saveLogsAsExcelReport2();
            }
            $excelWriter = $logTableList->returnXlsFile();

            $path = SimulationService::createPathForAnalyticsFile('custom', $assessment_version);

            $excelWriter->save($path);

        }
        echo "Done ".count($data_simulations)." \r\n";
    }
}