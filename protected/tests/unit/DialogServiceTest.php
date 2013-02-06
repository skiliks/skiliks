<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 06.02.13
 * Time: 18:37
 * To change this template use File | Settings | File Templates.
 */

class DialogServiceTest extends PHPUnit_Framework_TestCase {

    public function test_dialog_get() {

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);
        $standard = [
            'result' => 1,
            'events' => [
                [
                    'result' => 1,
                    'data' => [],
                    'eventType' => 1
                ]
            ]
        ];
        $this->assertEquals(
            (new DialogService())->getDialog(
                $simulation->id,
                Dialogs::model()->findByAttributes(['code' => 'ET1.1', 'replica_number'=>2])->id,
                '11:00'),
                $standard
        );
        $this->assertEquals(
            (new DialogService())->getDialog(
                $simulation->id,
                Dialogs::model()->findByAttributes(['code' => 'S1.1', 'replica_number'=>2])->id,
                '11:05'),
            $standard
        );

    }

}
