<?php
class MakeSimulationResultsCacheCommand extends CConsoleCommand {

    public function actionIndex($version = 'v1')
    {
        echo "Начинаем кешировать попапы симуляций: \n";

        $sims = Simulation::model()->findAll();

        if ('v1' === $version) {
            $method = 'getAssessmentDetailsV1';
        }

         // @var Simulation $sim
        foreach ($sims as $simulation) {
            if (null === $simulation->results_popup_cache) {
                // make cache! :)
                $simulation->results_popup_cache = serialize($simulation->{$method}());
                $simulation->save(false);
                echo "Симуляция $simulation->id : кеш добавлен. \n";
            } else {
                $simulation->results_popup_partials_path = '//static/dashboard/partials/';
                $simulation->save(false);
                echo "Симуляция $simulation->id : кеш уже был. \n";
            }
        }

        echo "Готово! \n";
    }
}