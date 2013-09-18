<?php
/**
 * Created by Vladimir Boyko Skilix.
 */

/**
 *
 * Выбирает полный лог всех юзеров из базы
 */
class ShowClientAnalyticsCommand extends CConsoleCommand
{
    private $language = ['management' => 'Управленческие навыки', 'overall' => 'Результативность', 'time' => ''];

    public function actionIndex($ids = false) // 7 days
    {
        if($ids) {
            $saves = 0;
            $overwrite = true;
            $ids = explode(",", $ids);
            if(!empty($ids)) {
                $simulations = array();
                foreach($ids as $row) {
                    $simulation = Simulation::model()->findByPk($row);
                    if($simulation !== null) {
                        $simulations[] = $simulation;
                        echo "{$simulation->id}, ";
                    }
                }

                if(!empty($simulations)) {
                    SimulationService::saveLogsAsExcelAnalysis2($simulations);
                }

                echo " {$saves} files stored!\r\n";
            }
        }
    }
}