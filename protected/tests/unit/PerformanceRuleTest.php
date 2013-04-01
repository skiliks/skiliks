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

    public function testExcelTrue() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);

        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Simulation::TYPE_FULL);

        $this->addExcelPoints($simulation);

        $res = SimulationExcelPoint::model()->findAllByAttributes(['sim_id'=>$simulation->id]);

        $ms = $simulation->game_type->getMailTemplate(['code'=>"MS36"]);

        $mail_box = new MailBox();
        $mail_box->group_id = 1;
        $mail_box->receiver_id = 1;
        $mail_box->sender_id = 1;
        $mail_box->subject_id = 10;
        $mail_box->sim_id = $simulation->id;
        $mail_box->template_id = $ms->id;
        $mail_box->code = 'MS36';
        $mail_box->coincidence_mail_code = 'full';
        $mail_box->coincidence_type = 'MS36';
        $mail_box->letter_type = '';
        $mail_box->save();

        $log_mail = new LogMail();
        $log_mail->mail_id = $mail_box->id;
        $log_mail->sim_id = $simulation->id;
        $log_mail->mail_task_id = null;
        $log_mail->full_coincidence = "MS36";
        $log_mail->start_time = '11:00:20';
        $log_mail->end_time = '11:20:30';
        $log_mail->window = 13;
        $log_mail->window_uid = '34';
        $log_mail->save();

        SimulationService::setFinishedPerformanceRules($simulation->id);

        $performanceRule = $simulation->game_type->getPerformanceRule(['code' => 40]);
        $rule = PerformancePoint::model()->findByAttributes(['sim_id' => $simulation->id, 'performance_rule_id' => $performanceRule->getPrimaryKey()]);

        $this->assertNotNull($rule);
    }

    public function testExcelFasle(){
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);

        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $this->addExcelPoints($simulation);

        SimulationService::setFinishedPerformanceRules($simulation->id);

        $rule = PerformancePoint::model()->findByAttributes(['sim_id' => $simulation->id, 'performance_rule_id' => 40]);

        $this->assertNull($rule);
    }

    public function addExcelPoints($simulation){
        /* @var SimulationExcelPoint $point  */
        for ($i = 1; $i <= 9; $i++) {
            $point = SimulationExcelPoint::model()->findByAttributes(['sim_id'=>$simulation->id, 'formula_id'=>$i]);
            if(null === $point){
                $point = new SimulationExcelPoint();
            }
            $point->formula_id = $i;
            $point->value = '1.00';
            $point->sim_id = $simulation->id;
            $point->save();
        }
    }

}
