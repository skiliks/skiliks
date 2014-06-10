<?php

/**
 * Class UpdateZeroPercentileCommand
 *
 * Ищет симуляции с нулевым процентилем в кеше попапа оценки,
 * проверяет нет ли в БД значения которое отличное от 0,
 * исправляет кеш.
 */
class UpdateZeroPercentileCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);

        /* @var Simulation[] $simulations */
        $simulations = Simulation::model()->findAll(
            "results_popup_cache is not null AND results_popup_cache not like '%percentile%' "
        );

        $count_update = 0;
        foreach($simulations as $simulation) {

            $percentile = AssessmentOverall::model()->findByAttributes([
                'sim_id'                   => $simulation->id,
                'assessment_category_code' => AssessmentCategory::PERCENTILE,
            ]);

            if (null != $percentile && 0.00 !== $percentile->value) {

                // fix amount
                $simulation->results_popup_cache = str_replace(
                    's:15:"additional_data";',
                    's:10:"percentile";a:1:{s:5:"total";s:'
                        . strlen((string)$percentile->value) . ':"'
                        . (string)$percentile->value . '";}s:15:"additional_data";',
                    $simulation->results_popup_cache
                );

                // fix array length
                $simulation->results_popup_cache = str_replace(
                    'a:6:{s:10:"management"',
                    'a:7:{s:10:"management"',
                    $simulation->results_popup_cache
                );

                $simulation->save(false);
                echo $simulation->id . ' - ' . $percentile->value . "\n";

                $count_update++;
            }
        }

        echo "Simulations update ".$count_update."\r\n";
    }
}