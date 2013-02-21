<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 12.02.13
 * Time: 16:19
 * To change this template use File | Settings | File Templates.
 */

class DialogueDelayTest extends CDbTestCase {

    public function testDelay(){
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        Tasks::model()->deleteAll();
        MailBoxModel::model()->deleteAll();
        $event = new EventsManager();

        //Запуск T7.1
        $this->setTime($simulation, 11, 12);
        $event->startEvent($simulation->id, 'T7.1', false, false, 0);
        for($i=0;$i<20;$i++){
            $json = $event->getState($simulation, false);
            if(!empty($json['events'][0]['eventType']) && $json['events'][0]['eventType'] == 1){
                Logger::write(var_export($json, true));

                break;
            }
        }
        $this->assertEquals('T7.1',$json['events'][0]['data'][0]['code']);

        $event->startEvent($simulation->id, 'RST2', false, false, 5);
        $event->startEvent($simulation->id, 'RST2', false, false, 5);

        //Запуск RST2
        $this->setTime($simulation, 11, 22, false);

        for($i=0;$i<4;$i++){
            $json = $event->getState($simulation, false);

            if(!empty($json['events'][0]['eventType']) && $json['events'][0]['eventType'] == 1){
                Logger::write(var_export($json, true));

                break;
            }
        }
        $this->assertEquals('RST2',$json['events'][0]['data'][0]['code']);

        $event->startEvent($simulation->id, 'S1.2', false, false, 2);

        $this->setTime($simulation, 11, 24, false);

        for($i=0;$i<4;$i++){
            $json = $event->getState($simulation, false);
            if(!empty($json['events'][0]['eventType']) && $json['events'][0]['eventType'] == 1){
                break;
            }
        }
        $this->assertEquals('S1.2',$json['events'][0]['data'][0]['code']);
        $simulation_service->simulationStop($simulation);
    }

    public function setTime($simulation, $newHours, $newMinutes, $s = true){
        SimulationService::setSimulationClockTime(
            $simulation,
            $newHours,
            $newMinutes
        );
        if($s == true){
            $simulation->deleteOldTriggers($newHours, $newMinutes);
        }

    }

}
