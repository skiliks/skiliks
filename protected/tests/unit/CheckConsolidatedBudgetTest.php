<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivan
 * Date: 06.03.13
 * Time: 17:18
 * To change this template use File | Settings | File Templates.
 */

class CheckConsolidatedBudgetTest extends CDbTestCase
{

    public function testFormula()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $CheckConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $CheckConsolidatedBudget->calcPoints(__DIR__ . '/files/D1.xls');

        $points = SimulationExcelPoint::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $this->assertNotNull($points);

        if ($points !== null) {
            foreach ($points as $point) {
                $this->assertEquals('1.00', $point->value);
            }
        }
    }

    public function testFormulaForNew()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $CheckConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $CheckConsolidatedBudget->calcPoints(__DIR__ . '/files/D1_new.xls');

        $points = SimulationExcelPoint::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $this->assertNotNull($points);

        if ($points !== null) {
            foreach ($points as $point) {
                $this->assertEquals('0.00', $point->value);
            }
        }
    }

}
