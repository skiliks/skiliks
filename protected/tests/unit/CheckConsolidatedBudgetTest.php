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
        $budgetPath = __DIR__ . '/files/D1.sc';

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $checkConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $checkConsolidatedBudget->calcPoints($budgetPath);

        $points = SimulationExcelPoint::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $this->assertNotNull($points);

        if ($points !== null) {
            foreach ($points as $point) {
                $this->assertEquals('1.00', $point->value, $point->formula_id);
            }
        }

    }

    /*
     * Проверка на то, что если в D1 нет изминений, то пользователь получит 9 нулей
     */

    public function testFormulaForNew()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $CheckConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $CheckConsolidatedBudget->calcPoints(__DIR__ . '/files/D1.sc');

        $points = SimulationExcelPoint::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $this->assertNotNull($points);

        if ($points !== null) {
            foreach ($points as $point) {
                $this->assertEquals('0.00', $point->value, $point->formula_id);
            }
        }
    }

}
