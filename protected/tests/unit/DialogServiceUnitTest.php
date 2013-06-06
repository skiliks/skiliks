<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 06.02.13
 * Time: 18:37
 * To change this template use File | Settings | File Templates.
 */

class DialogServiceUnitTest extends PHPUnit_Framework_TestCase
{

    public function testDialogGet()
    {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $standard = [
            'result' => 1,
            'events' => []
        ];

        $res = (new DialogService())->getDialog(
            $simulation->id,
            Replica::model()->findByAttributes(['code' => 'ET1.1', 'replica_number' => 2])->id,
            '11:00');

        $this->assertEquals($res, $standard);

        $this->assertEquals(
            (new DialogService())->getDialog(
                $simulation->id,
                Replica::model()->findByAttributes(['code' => 'S1.1', 'replica_number' => 2])->id,
                '11:05'),
            $standard
        );

    }

    public function testDialogGetForDialogAndPlan()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $standardExcelIds = ['260', '261', '262', '263'];

        $results = (new DialogService())->getDialog(
            $simulation->id,
            Replica::model()->findByAttributes(['code' => 'E3.5', 'step_number'=>5, 'replica_number' => 1])->id,
            '11:00');

        foreach ($results['events'][0]['data'] as $replica) {
            $this->assertTrue(in_array($replica['excel_id'], $standardExcelIds), $replica['excel_id']);
        }

        //$this->assertEquals($standard, $res);

        $template = $simulation->game_type->getEventSample(['code'=>'P30']);

        $ev = EventTrigger::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'event_id' => $template->id
        ]);

        /* @var $ev EventTrigger */

        $this->assertEquals("09:47", date("H:i", strtotime($ev->trigger_time)));
    }



}
