<?php
/**
 * Created by Vladimir Boyko Skilix.
 */

/**
 *
 * Выбирает полный лог всех юзеров из базы
 */
class ShowUserLogsCommand extends CConsoleCommand
{
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
                    if($simulation !== null) $simulations[] = $simulation;
                }
                if(!empty($simulations)) {
                    SimulationService::saveLogsAsExcelCombined($simulations);
                }
                echo " {$saves} files stored!\r\n";
            }
        }
    }
}