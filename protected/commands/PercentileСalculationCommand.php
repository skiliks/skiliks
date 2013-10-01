<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class PercentileСalculationCommand extends CConsoleCommand {

    public function actionIndex()
    {

        $simulations = Simulation::model()->getRealUsersSimulations();

        foreach($simulations as $simulation) {
            $simulation->calculatePercentile();
            $simulation->save();
        }

        echo "Done!";
    }
}