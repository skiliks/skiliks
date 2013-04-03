<?php

/**
 *
 */
class PhoneServiceTest extends CDbTestCase
{

    /**
     * Проверяет правильность имени персонажа при пропущеном звонке
     */
    public function testGetMissedCalls()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);

        // init test data {
        $toCharacter = Character::model()->find([
            'condition' => 'id NOT IN (:id)',
            'params' => [
                'id' => Character::HERO_ID
            ]
        ]);

        $time = '11:00:00';

        $phoneCallHistoryRecord = new PhoneCall();
        $phoneCallHistoryRecord->sim_id = $simulation->id;
        $phoneCallHistoryRecord->call_type = PhoneCall::MISSED_CALL;
        $phoneCallHistoryRecord->from_id = $toCharacter->id;
        $phoneCallHistoryRecord->to_id = Character::HERO_ID;
        $phoneCallHistoryRecord->call_time = $time;
        $phoneCallHistoryRecord->save();
        // init test data }

        // run targer method
        $missedCalls = PhoneService::getMissedCalls($simulation);

        // assertions:

        // we have just one missed call
        $this->assertEquals(1, count($missedCalls));

        // check this call values
        $missedCall = reset($missedCalls);
        $this->assertTrue(in_array($missedCall['name'], [$toCharacter->fio, $toCharacter->title]), 'Wrong character call from name');
        $this->assertEquals(Simulation::formatDateForMissedCalls($time, '04.10.2012'), $missedCall['date'], 'Wrong call date');
        $this->assertEquals(2, $missedCall['type'], 'Wrong call type');
    }

    /**
     * Проверяет правильность имени персонажа при пропущеном звонке
     *
     * @group g1
     */
    public function testSetCallHistory()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);

        // init test data {
        $time = ['09:06', '11:45', '11:50'];
        $dialogCode = ['RST10', 'RST3', 'ET1.1'];
        $replicas = [];
        $toCharacters = [];

        for ($i = 0; $i < 2; $i++) {
            $replicas[$i] = Replica::model()->find([
                'condition' => " code = :code AND step_number = :sn AND replica_number = :rn  ",
                'params' => [
                    'code' => $dialogCode[$i],
                    'sn' => 1,
                    'rn' => 2,
                ]
            ]);

            $eventsManager = new EventsManager();
            EventsManager::startEvent($simulation, $dialogCode[$i], 0, 0, 0); // init call from friend

            $dialogService = new DialogService();
            $dialogService->getDialog($simulation->id, $replicas[$i]->id, $time[$i]); // init ignore call fron friend


            $toCharacters[$i] = Character::model()->findByPk($replicas[$i]->ch_to); // friend
        }

        // init test data }

        // run targer method
        $missedCalls = PhoneService::getMissedCalls($simulation);

        // assertions:

        // we have just one missed call
        $this->assertEquals(2, count($missedCalls));

        // check this call values

        for ($i = 0; $i < 2; $i++) {
            $this->assertTrue(
                in_array($missedCalls[$i]['name'],
                    [$toCharacters[$i]->fio, $toCharacters[$i]->title]),
                'Wrong character call from name ' . $missedCalls[$i]['name'] . ' i=' . $i);
            $this->assertEquals(
                $missedCalls[$i]['date'],
                Simulation::formatDateForMissedCalls($time[$i] . ':00', '04.10.2012'),
                'Wrong call date' . ' i=' . $i);
            $this->assertEquals(
                $missedCalls[$i]['type'],
                2,
                'Wrong call type' . ' i=' . $i);
        }
    }

    /**
     * Проверяет исходящие звонки. В роли собеседника выбран Трутнев
     */
    public function testOutgoingCall()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);

        $time = sprintf('%02d:%02d', rand(8, 11), rand(0, 59));
        $characterCode = 3; // Трутнев

        $character = Character::model()->findByAttributes(['code' => $characterCode]);
        $theme = CommunicationTheme::model()->byCharacter($character->primaryKey)->byText('Задача отдела логистики: статус')->byPhone()->find();

        $this->assertInstanceOf('CommunicationTheme', $theme);

        $result = PhoneService::call($simulation, $theme->id, $characterCode, $time);
        $this->assertEquals(1, $result['result']);
        $this->assertEquals(1, $result['events'][0]['result']);
        $this->assertEquals(3, $result['events'][0]['data'][0]['ch_from']);
        $this->assertEquals(1, $result['events'][0]['data'][0]['ch_to']);
        $this->assertEquals('T7.1', $result['events'][0]['data'][0]['code']);
        $this->assertEquals(561, $result['events'][0]['data'][0]['excel_id']);

        $this->assertEquals(1, $result['events'][0]['data'][1]['ch_from']);
        $this->assertEquals(3, $result['events'][0]['data'][1]['ch_to']);
        $this->assertEquals('T7.1', $result['events'][0]['data'][1]['code']);
        $this->assertEquals(562, $result['events'][0]['data'][1]['excel_id']);
    }

    public function testOnlyUniqueCall()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_LABEL, $user, Scenario::TYPE_FULL);
        $character_id = $simulation->game_type->getCharacter(['fio' => 'Денежная Р.Р.'])->getPrimaryKey();
        $theme_id = CommunicationTheme::model()->findByAttributes(['text' => 'Перенос сроков сдачи сводного бюджета', 'character_id' => $character_id, 'phone' => 1])->id;
        $data = PhoneService::call($simulation, $theme_id, $character_id, '10:00');

        $this->assertNotEquals([], $data['events']);
        $data = PhoneService::call($simulation, $theme_id, $character_id, '10:10');
        $this->assertEquals([], $data['events']);
        SimulationService::simulationStop($simulation);
    }
}

