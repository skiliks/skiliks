<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 06.02.13
 * Time: 13:17
 * To change this template use File | Settings | File Templates.
 */

class EventServiceTest extends PHPUnit_Framework_TestCase {

    public function test_add_by_code() {

        $events = [
            ['code'=>'S1.1', 'time'=>'11:05:00', 'standard_time'=>'11:10:00'],
            ['code'=>'S1.2', 'time'=>'11:10:00', 'standard_time'=>'11:20:00']
        ];
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        foreach($events as $e){
            EventService::addByCode($e['code'], $simulation->id, $e['time']);
            $event = EventsSamples::model()->byCode($e['code'])->find();
            $event = EventsTriggers::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
            $this->assertEquals($e['standard_time'], $event->trigger_time);
        }

    }

    /*
     * Проверка на то что если E1 был запущен, то его уже нет в стеке событий
     */
    public function testEventNotStart() {
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);
        $dialog = new DialogService();
        $dialog_cancel = Dialogs::model()->findByAttributes(['code'=>'S1.1', 'replica_number'=> 1]);
        $dialog->getDialog($simulation->id, $dialog_cancel->id, '9:05');
        $dialog_call = Dialogs::model()->findByAttributes(['code'=>'E1', 'replica_number'=> 0]);
        $dialog->getDialog($simulation->id, $dialog_call->id, '9:06');
        $event = EventsSamples::model()->findByAttributes(['code'=>'E1']);
        $res = EventsTriggers::model()->findByAttributes(['event_id' => $event->id, 'sim_id'=>$simulation->id]);
        $this->assertEquals(null, $res);
    }

}
