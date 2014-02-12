<?php
/**
 * Генерирует сводный аналитический файл по всем завершенным симуляция
 * v1 или v2
 * и сохраняет в папку protected/system_data/analytic_files_2/*
 */
class GenerateReportCustomCommand extends CConsoleCommand
{
    public function actionIndex($assessment_version = 'v2')
    {
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
            if(isset($categories_percentile[$simulation->id]) && $categories_percentile[$simulation->id] != 0 && empty($simulation->results_popup_cache) === false) {

                $data_simulations[$simulation->id] = $simulation;

            }
        }

        // также нам нужны симуляции от e.sarnova@august-bel.by
        $profile = YumProfile::model()->findByAttributes(['email' => 'e.sarnova@august-bel.by']);
        $simulations = Simulation::model()->findAll(
            "user_id = {$profile->user_id} and
                scenario_id = {$scenario->id} and
                assessment_version = '{$assessment_version}' and
                end is not null");
        foreach($simulations as $simulation) {
            $data_simulations[$simulation->id] = $simulation;
        }

        echo "Calc ".count($data_simulations)." \r\n";

        if(!empty($data_simulations)) {

            $generator = new AnalyticalFileGenerator();
            $generator->{"runAssessment_".$assessment_version}($data_simulations);
        }

        echo "Done ".count($data_simulations)." \r\n";
    }
}