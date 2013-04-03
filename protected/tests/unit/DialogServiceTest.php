<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 06.02.13
 * Time: 18:37
 * To change this template use File | Settings | File Templates.
 */

class DialogServiceTest extends PHPUnit_Framework_TestCase
{

    public function testDialogGet()
    {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);
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



}
