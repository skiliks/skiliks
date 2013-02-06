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
        $code = "S1.1";
        $time = "11:05:00";
        $standard_time = "11:10:00";
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        EventService::addByCode($code, $simulation->id, $time);

        $event = EventsSamples::model()->byCode($code)->find();
        $event = EventsTriggers::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
        $this->assertEquals($standard_time, $event->trigger_time);
    }

}
