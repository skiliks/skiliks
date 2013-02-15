<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 2/15/13
 * Time: 11:24 AM
 * To change this template use File | Settings | File Templates.
 */
class Flags_2_Test extends CDbTestCase
{
    /**
     * Проверяет что на фронтенд попадают только правильные реплики по диалогу S2
     */
    public function testBlockReplica()
    {
        //$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        // case 1

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'S2', false, false, 0);

        $result = $e->getState($simulation, []);

        foreach ($result['events'][0]['data'] as $replicaDataArray) {
            $this->assertTrue(in_array($replicaDataArray['id'], [134, 135, 136]));
        }

        // case 2
        // @todo: finalize

//        FlagsService::setFlag($simulation->id, 'F1', 1);
//
//        $e = new EventsManager();
//        $e->startEvent($simulation->id, 'S2', true, true, 0);
//
//        $result = $e->getState($simulation, []);
//
//        foreach ($result['events'][0]['data'] as $replicaDataArray) {
//            //$this->assertTrue(in_array($replicaDataArray['id'], []));
//        }
    }

    public function testBlokDialog()
    {
        $this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        // FlagsService::setFlag($simulation->id, 'F4', 1);

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'ET1.3.1', false, false, 0);

        $result = $e->getState($simulation, []);

        $this->assertFalse(isset($result['events']));
    }

    public function testSendEmailAfterFladSwitched()
    {
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        FlagsService::setFlag($simulation->id, 'F14', 1);

        $email = MailBoxModel::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M10'
        ]);

        $e = new EventsManager();
        $result = $e->getState($simulation, []);

        var_dump($result);

        //$this->assertEquals('1', $email->group_id);
    }
}
