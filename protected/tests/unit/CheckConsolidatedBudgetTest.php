<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivan
 * Date: 06.03.13
 * Time: 17:18
 * To change this template use File | Settings | File Templates.
 */

class CheckConsolidatedBudgetTest extends CDbTestCase {

    public function testFormula(){

        $simulation_service = new SimulationService();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);
        $CheckConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $CheckConsolidatedBudget->calcPoints(__DIR__.'/files/D1.xls');
        $points = SimulationExcelPoint::model()->findAllByAttributes(['sim_id'=>$simulation->id]);
        $this->assertNotNull($points);
        if($points !== null){
            foreach($points as $point){
                $this->assertEquals('1.00', $point->value);
            }
        }

        //$simulation_service->simulationStop($simulation);

    }

}
