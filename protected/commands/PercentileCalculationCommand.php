<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class PercentileCalculationCommand extends CConsoleCommand {

    public function actionIndex()
    {

        $simulations = Simulation::model()->getRealUsersSimulations();

        var_dump(count($simulations));

        echo "Start\n";

        foreach($simulations as $simulation) {
            $simulation->calculatePercentile();
            $simulation->save();
            echo $simulation->invite->getPercentile() . " " . $simulation->id . "\n";
        }

        echo "\nDone!\n";
    }
}