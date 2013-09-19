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
        // 5359, 5364, 5358, 5355, 5350, 5348, 5353, 5240, 5233, 5234, 5221, 5223, 5230,
        // 5224, 5222, 5217, 5214, 5213, 5227, 5229, 5239, 5168, 5153, 5154, 5150, 5144,
        // 5145, 5142, 5146, 5158, 5160, 5016, 5018, 5011, 5014, 5017, 5013, 5009, 4997, 4995

        $ids = '5359, 5364, 5358, 5355, 5350, 5348, 5353, 5240, 5233, 5234, 5221, 5223, 5230, 5224, 5222, 5217, 5214, 5213, 5227, 5229, 5239, 5153, 5154, 5150, 5144, 5145, 5142, 5146, 5158, 5160, 5016, 5018, 5011, 5014, 5017, 5013, 5009, 4997, 4995';
        //$ids = '5359';

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