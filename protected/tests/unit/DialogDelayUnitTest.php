<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 12.02.13
 * Time: 16:19
 * To change this template use File | Settings | File Templates.
 */

class DialogDelayUnitTest extends CDbTestCase
{

    /**
     *
     */
    public function testDelay()
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {

            $user = YumUser::model()->findByAttributes(['username' => 'asd']);
            $invite = new Invite();
            $invite->scenario = new Scenario();
            $invite->receiverUser = $user;
            $invite->scenario->slug = Scenario::TYPE_FULL;
            $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

            // we need transaction - this test delete empty Task table
            $event = new EventsManager();

            //Запуск T7.1
            $this->setTime($simulation, 11, 12);
            EventsManager::startEvent($simulation, 'T7.1', false, false, 0);
            for ($i = 0; $i < 10; $i++) {
                $json = EventsManager::getState($simulation, false);
                if (!empty($json['events'][0]['eventType']) && $json['events'][0]['eventType'] == 1) {
                    break;
                }
            }
            if(!empty($json['events'][0]['data'][0]['code'])){
                $this->assertEquals('T7.1', $json['events'][0]['data'][0]['code']);
            }

            EventsManager::startEvent($simulation, 'RST2', false, false, 5);
            EventsManager::startEvent($simulation, 'RST2', false, false, 5);

            //Запуск RST2
            $this->setTime($simulation, 11, 22, false);

            for ($i = 0; $i < 10; $i++) {
                $json = EventsManager::getState($simulation, false);

                if (!empty($json['events'][0]['eventType']) && $json['events'][0]['eventType'] == 1) {
                    break;
                }
            }
            if(!empty($json['events'][0]['data'][0]['code'])){
                $this->assertEquals('RST2', $json['events'][0]['data'][0]['code']);
            }
            EventsManager::startEvent($simulation, 'S1.2', false, false, 2);

            $this->setTime($simulation, 11, 24, false);

            for ($i = 0; $i < 10; $i++) {
                $json = EventsManager::getState($simulation, false);
                if (!empty($json['events'][0]['eventType']) && $json['events'][0]['eventType'] == 1) {
                    break;
                }
            }
            if(!empty($json['events'][0]['data'][0]['code'])){
                $this->assertEquals('S1.2', $json['events'][0]['data'][0]['code']);
            }
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    /**
     * Service method
     *
     * @param $simulation
     * @param $newHours
     * @param $newMinutes
     * @param bool $s
     */
    public function setTime($simulation, $newHours, $newMinutes, $s = true)
    {
        SimulationService::setSimulationClockTime(
            $simulation,
            $newHours,
            $newMinutes
        );
        if ($s == true) {
            $simulation->deleteOldTriggers($newHours, $newMinutes);
        }

    }

}
