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
     * 
     * @group g1
     */
    public function testSetCallHistory() 
    {
        // init simulation
        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(1, $user);
        
        // init test data {
        $time = ['09:06', '11:45', '11:50'];
        $dialogCode = ['RST10', 'RST3', 'ET1.1'];
        $replicas = [];
        $toCharacters = [];
        
        for ($i = 0; $i < 2; $i++) {
            $replicas[$i] = Dialogs::model()->find([
                'condition'  => " code = :code AND step_number = :sn AND replica_number = :rn  ",
                'params'     => [
                    'code' => $dialogCode[$i],
                    'sn'   => 1,
                    'rn'   => 2,
                ]
            ]);
            
            $eventsManager = new EventsManager();
            $eventsManager->startEvent($simulation->id, $dialogCode[$i], 0, 0, 0); // init call from friend

            $dialogService = new DialogService();
            $dialogService->getDialog($simulation->id, 	$replicas[$i]->id , $time[$i]); // init ignore call fron friend


            $toCharacters[$i] = Characters::model()->findByPk($replicas[$i]->ch_to); // friend
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
                'Wrong character call from name '.$missedCalls[$i]['name'].' i='.$i);
            $this->assertEquals(
                $missedCalls[$i]['date'],          
                Simulations::formatDateForMissedCalls($time[$i].':00'), 
                'Wrong call date'.' i='.$i);
            $this->assertEquals(
                $missedCalls[$i]['type'],          
                2,                                                     
                'Wrong call type'.' i='.$i);  
        }
    }
}

