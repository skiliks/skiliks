<?php
/**
 * Команда генерирует аналитический файл по всем прохождениям реальных людей
 *
 * Ексель состоит из пяти листов:
 * - оценки верхнего уровня
 * - управленческие навыки подробно
 * - результативность подробно
 * - управление временем подробно
 * - баллы за поведения за игру
 *
 * и сохраняет в папку protected/system_data/analytic_files_2/*
 */
class GenerateReport3Command extends CConsoleCommand
{
    public function actionIndex($assessment_version = 'v2')
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 60*180);

        $start_time = time();
        // Собираем процентили {
        // по ним мы будем определять это реальная симуляция или прохождение тестировщика/разбаботчика
        $percentiles = AssessmentOverall::model()->findAllByAttributes([
            'assessment_category_code' => AssessmentCategory::PERCENTILE,
        ]);

        foreach($percentiles as $percentile) {
            $simulationPercentiles[$percentile->sim_id] = $percentile->value;
        }
        // Собираем процентили }

        // Собираем все симуляции и группируем по типу оценки [v1,v2] {
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);

        /* @var Simulation[] $simulations */
        $allSimulations = Simulation::model()->findAll(
            "scenario_id = {$scenario->id} AND end IS NOT NULL");

        $realUserSimulationsV1 = [];
        $realUserSimulationsV2 = [];

        foreach($allSimulations as $simulation) {

            /* @var Simulation $simulation */
              if(isset($simulationPercentiles[$simulation->id])
                && $simulationPercentiles[$simulation->id] != 0
                && empty($simulation->results_popup_cache) === false) {

                /* @var Simulation $simulation */
                if (Simulation::ASSESSMENT_VERSION_1 == $simulation->assessment_version) {
                    $realUserSimulationsV1[$simulation->id] = $simulation;
                }

                if (Simulation::ASSESSMENT_VERSION_2 == $simulation->assessment_version) {
                    $realUserSimulationsV2[$simulation->id] = $simulation;
                }
            }
        }
        // Собираем и группируем симуляции }

        // также нам нужны симуляции от e.sarnova@august-bel.by {
        $augustBelProfile = YumProfile::model()->findByAttributes(['email' => 'e.sarnova@august-bel.by']);
        $augustBelSimulations = Simulation::model()->findAll(
            "user_id = {$augustBelProfile->user_id} and
                scenario_id = {$scenario->id} and
                assessment_version = '{$assessment_version}' and
                end is not null");

        foreach($augustBelSimulations as $simulation) {
            /* @var Simulation $simulation */
            if (Simulation::ASSESSMENT_VERSION_1 == $simulation->assessment_version) {
                $realUserSimulationsV1[$simulation->id] = $simulation;
            }

            if (Simulation::ASSESSMENT_VERSION_2 == $simulation->assessment_version) {
                $realUserSimulationsV2[$simulation->id] = $simulation;
            }
        }
        // также нам нужны симуляции от e.sarnova@august-bel.by }

        // салютуем в консоль, что данные готовы
        var_dump(date('H:i:s', $start_time));
        var_dump(date('H:i:s', time()));
        echo "Calc ".(count($realUserSimulationsV1) + count($realUserSimulationsV2))." \r\n";

        // непосредственно генерация
        $generator = new AnalyticalFileGenerator();
        $generator->is_add_behaviours = true;
        $generator->createDocument();
        echo "\r\nV1:";
        $generator->runAssessment_v1($realUserSimulationsV1, 'v1_to_v2');
        echo "\r\nV2:";
        $generator->runAssessment_v2($realUserSimulationsV2);
        $generator->save('','full_report');

        echo "Done ".(count($realUserSimulationsV1) + count($realUserSimulationsV2))." \r\n";
        var_dump(date('H:i:s', $start_time));
        var_dump(date('H:i:s', time()));
    }
}