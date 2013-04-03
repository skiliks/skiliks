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
        /*
         * Проверка оценок по эталону
         */

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);
        SimulationService::simulationStop($simulation);

        $budgetPath = __DIR__ . '/files/D1.xls';

        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);
        $checkConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $checkConsolidatedBudget->calcPoints($budgetPath);

        $points = SimulationExcelPoint::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $this->assertNotNull($points);

        if ($points !== null) {
            foreach ($points as $point) {
                $this->assertEquals('1.00', $point->value);
            }
        }

    }

    /*
     * Проверка на то, что если в D1 нет изминений, то пользователь получит 9 нулей
     */

    public function testFormulaForNew()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);

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
