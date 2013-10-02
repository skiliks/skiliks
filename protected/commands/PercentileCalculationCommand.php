<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class PercentileCalculationCommand extends CConsoleCommand {

    public function actionIndex()
    {

        $simulations = Simulation::model()->getRealUsersSimulations();

        echo "Start\n";

        foreach($simulations as $simulation) {
            $simulation->calculatePercentile();
            $simulation->save();
            echo '.';
        }

        echo "\nDone!\n";
    }
}