<?php

/**
 * 
 */
class PhoneServiceTest extends CDbTestCase {
    
    /**
     * Проверяет правильность имени персонажа при пропущеном звонке
     */
    public function testGetMissedCalls() 
    {
        // init simulation
        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(1, $user);
        
        // init test data {
        $toCharacter = Characters::model()->find([
            'condition' => 'id NOT IN (:id)',
            'params'    => [
                'id' => Characters::HERO_ID
            ]
        ]);
        
        $time = '11:00:00';
        
        $phoneCallHistoryRecord            = new PhoneCallsModel();
        $phoneCallHistoryRecord->sim_id    = $simulation->id;
        $phoneCallHistoryRecord->call_type = PhoneCallsModel::MISSED_CALL; 
        $phoneCallHistoryRecord->from_id   = $toCharacter->id;
        $phoneCallHistoryRecord->to_id     = Characters::HERO_ID;
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
        $this->assertEquals(Simulations::formatDateForMissedCalls($time), $missedCall['date'], 'Wrong call date');
        $this->assertEquals(2, $missedCall['type'], 'Wrong call type');       
    }
    
    /**
     * Проверяет правильность имени персонажа при пропущеном звонке
     */
    public function testSetCallHistory() 
    {
        // init simulation
        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(1, $user);
        
        // init test data {
        $time = '09:06';
        
        $eventsManager = new EventsManager();
        $eventsManager->startEvent($simulation->id, 'RST10', 0, 0, 0); // init call from friend
        
        $dialogService = new DialogService();
        $dialogService->getDialog($simulation->id, 	991, $time); // init ignore call fron friend
        
        $toCharacter = Characters::model()->findByPk(28); // friend
        
        
        // init test data }
        
        // run targer method
        $missedCalls = PhoneService::getMissedCalls($simulation);
        
        // assertions:

        // we have just one missed call
        $this->assertEquals(1, count($missedCalls));
        
        // check this call values
        $missedCall = reset($missedCalls);        
        $this->assertTrue(in_array($missedCall['name'], [$toCharacter->fio, $toCharacter->title]), 'Wrong character call from name');
        $this->assertEquals(Simulations::formatDateForMissedCalls($time.':00'), $missedCall['date'], 'Wrong call date');
        $this->assertEquals(2, $missedCall['type'], 'Wrong call type');  
    }
}

