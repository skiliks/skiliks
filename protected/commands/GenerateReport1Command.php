<?php
/**
 * Created by Vladimir Boyko Skilix.
 */

/**
 * Выбирает полные логи всех симуляций из базы по $ids
 * и комбинирует в сводную
 */
class GenerateReport1Command extends CConsoleCommand
{
    public function actionIndex($ids = false)
    {
        if($ids) {
            $ids = explode(",", $ids);
            if(!empty($ids)) {
                $simulations = array();
                foreach($ids as $row) {
                    $simulation = Simulation::model()->findByPk($row);

                    echo "Combine simulations: ";
                    if($simulation !== null) {
                        $simulations[] = $simulation;
                        echo "{$simulation->id}, ";
                    }
                    echo "\r\n";
                }

                if(!empty($simulations)) {
                    SimulationService::saveLogsAsExcelReport1($simulations);
                }

                echo "File stored!\r\n";
            }
        }
    }
}