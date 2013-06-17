<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 02.03.13
 * Time: 1:14
 * To change this template use File | Settings | File Templates.
 */

class LogMailUnitTest extends PHPUnit_Framework_TestCase
{
    use UnitLoggingTrait;

    public function testActivityOverflow()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $message = LibSendMs::sendMs($simulation, 'MS20');
        $logs = [];
        $this->appendWindow($logs, 13);
        $this->appendDialog($logs, 'E1', 11);
        $this->appendNewMessage($logs, $message, 60, 1);

        EventsManager::processLogs($simulation, $logs);

        array_map(function ($i) {
            $i->dump();
        }, $simulation->log_mail);

        array_map(function ($i) {
            $i->dump();
        }, $simulation->log_activity_actions);

        SimulationService::simulationStop($simulation);
        $this->assertCount(2, $simulation->log_mail);
    }
}
