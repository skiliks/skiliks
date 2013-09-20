<?php
/**
 * Created by Vladimir Boyko Skilix.
 */

/**
 *
 * Выбирает полный лог всех юзеров из базы
 */
class GenerateReport2Command extends CConsoleCommand
{
    public function actionIndex($ids = false) // 7 days
    {
        // eksmo
        // $ids = '5359, 5364, 5358, 5355, 5350, 5348, 5353, 5240, 5233, 5234, 5221, 5223, 5230, 5224, 5222, 5217, 5214, 5213, 5227, 5229, 5239, 5153, 5154, 5150, 5144, 5145, 5142, 5146, 5158, 5160, 5016, 5018, 5011, 5014, 5017, 5013, 5009, 4997, 4995';

        // test
        // $ids = '5359';

        if($ids) {
            $ids = explode(",", $ids);
            if(!empty($ids)) {
                $simulations = array();

                echo "Combine simulations: ";

                foreach($ids as $id) {
                    $simulation = Simulation::model()->findByPk($id);
                    if($simulation !== null) {
                        $simulations[] = $simulation;
                        echo "{$simulation->id}, ";
                    }
                }

                echo "\r\n";

                if(!empty($simulations)) {
                    SimulationService::saveLogsAsExcelReport2($simulations);
                }

                echo "File stored!\r\n";
            }
        }
    }
}