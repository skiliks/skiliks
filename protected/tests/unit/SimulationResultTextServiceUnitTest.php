<?php

class SimulationResultTextServiceUnitTest extends CDbTestCase {

    public function testGenerate(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);
        $simulation->results_popup_cache = serialize(json_decode('{"management":{"1":{"1_1":{"+":"15.09","-":"0.00"}}}}', true));
        $simulation->save(false);

        $recommendations = SimulationResultTextService::generate($simulation, 'popup', true);

        $this->assertEquals([
            'management.day_planing.positive' => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
            'management.day_planing.negative' => 'В работе по планированию не было грубых ошибок',
            'management.day_planing' => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.'
        ], $recommendations);
    }

}
 