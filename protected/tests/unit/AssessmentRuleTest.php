<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 01.03.13
 * Time: 17:19
 * To change this template use File | Settings | File Templates.
 */

class AssessmentRuleTest extends CDbTestCase {
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
    public function testAssessment1(){
        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        /** @var $simulation Simulation */
        $simulation = $simulationService->simulationStart(Simulation::TYPE_PROMOTION, $user);
        $mgr = new EventsManager();
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

        #array_map(function ($i) {$i->dump();}, $simulation->log_mail);
        #array_map(function ($i) {$i->dump();}, $simulation->log_windows);
        #array_map(function ($i) {$i->dump();}, $simulation->log_activity_actions);
        print_r($logs);
        $mgr->processLogs($simulation, $logs);
        $simulationService->simulationStop($simulation);
        $this->assertEquals([9,10,11,12, 13], array_map(function ($i) {return $i->assessmentRule->id;}, $simulation->getAssessmentRules()));
    }

}
