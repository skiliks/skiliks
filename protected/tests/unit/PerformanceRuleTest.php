<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 01.03.13
 * Time: 17:19
 * To change this template use File | Settings | File Templates.
 */

class PerformanceRuleTest extends CDbTestCase {
    use UnitLoggingTrait;

    /**
        1. Запустить T7.1 - дойти до реплики 571
        2. Через 10 мин ответить на TT7.1.1
        3. В T7.1.1 дойти до реплики 578
        4. Запустить T7.2 - дойти до реплики 591
        5. Запустить T7.3 - дойти до реплики 596
        6. Запустить T7.4 - дойти до реплики 601
        7. Написать письмо MS45 и сохранить в Черновики
        8. Запустить T7.5 - дойти до реплики 605
        9. Отправить MS45 из Черновиков

        Оценка должна быть 5
     */
    public function testAssessment1()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);

        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $logs = [];
        $this->appendDialog($logs, 'T7.1', 571);
        $this->appendSleep($logs, 10*60);
        $this->appendDialog($logs, 'T7.1.1', 578);
        $this->appendDialog($logs, 'T7.2', 591);
        $this->appendDialog($logs, 'T7.3', 596);
        $this->appendDialog($logs, 'T7.4', 601);

        $message = LibSendMs::sendMs($simulation, 'MS45', true);

        $this->appendNewMessage($logs, $message);
        $this->appendDialog($logs, 'T7.5', 605);
        $this->appendMessage($logs, $message);
        MailBoxService::sendDraft($simulation, $message);


        EventsManager::processLogs($simulation, $logs);

        array_map(function ($i) {$i->dump();}, $simulation->log_activity_actions);

        SimulationService::simulationStop($simulation);
        $this->assertEquals([9,10,11,12, 13], array_map(function ($i) {return $i->performanceRule->id;}, $simulation->performance_points));
    }

    public function testExcel() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);

        $budgetPath = __DIR__ . '/files/D1.xls';

        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        $checkConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $checkConsolidatedBudget->calcPoints($budgetPath);

        SimulationService::setFinishedPerformanceRules($simulation->id);

        $rule = PerformancePoint::model()->findByAttributes(['sim_id' => $simulation->id, 'performance_rule_id' => 40]);

        $this->assertNotNull($rule);
    }

}
