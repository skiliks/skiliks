<?php

/**
 *
 * @author slavka
 */
class SimulationServiceTest extends CDbTestCase
{
    /**
     * Проверяет правильность оценивания игрока по за поведение 1122 
     * (оценивание обычным способом, лог писем пуст) 
     * оценка = максимальный_балл * (количество_правильных_проявления / количество_проявления_по_поведения_в_целом)
     */
    public function testCalculateAgregatedPointsFor1122() 
    {
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        // init conts
        // get all replics that change score for behaviour '1122'
        $replicsFor_1122 = Dialogs::model()->findAll('excel_id IN (210, 214, 311, 323, 424, 710, 714, 715, 766, 770, 211, 213, 235, 312, 322, 423, 521, 653, 656, 711, 713, 767, 769, 771)');
        
        $count_0 = 0;
        $count_1 = 0;
        
        // get 1122
        $pointFor_1122 = CharactersPointsTitles::model()->find('code = :code', ['code' => '1122']);  
        
        // init logs
        foreach($replicsFor_1122 as $dialogEntity) {
            LogHelper::setLogDoialogPoint( $dialogEntity->id, $simulation->id, $pointFor_1122->id);
            
            $dialogsPoint = CharactersPoints::model()->find('dialog_id = :dialog_id AND point_id = :point_id',[
                'dialog_id' => $dialogEntity->id,
                'point_id'  => $pointFor_1122->id
            ]);
            
            if ($dialogsPoint->add_value === '1') {
                $count_1++;
            }
            if ($dialogsPoint->add_value === '0') { // not else!
                $count_0++;
            }
        }
        
        // calculate point total scores
        SimulationService::saveAgregatedPoints($simulation->id);
        
        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);
        
        // assertions:
        $this->assertEquals(count($replicsFor_1122), ($count_0 + $count_1), 'Wrong replics add_value values!');
        
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');
        
        $is_1122_scored = false;
        
        foreach ($assessments as $assessment) {  
            if ($assessment->point->code === '1122') {
                // check 1122 is rigth
                // floor because 8.3333 is not equals to 8.3333333333333 in phpunit! :)
                $this->assertEquals(
                    floor($assessment->value*1000), 
                    floor(($count_1/($count_0 + $count_1))*$assessment->point->scale*1000), 
                    'Wrong 1122 value'
               );
               $is_1122_scored = true;
            } else {
                // check outer points is 0
                $this->assertEquals($assessment->value, 0, 'Wrong not acted behaviour value');
            }
        }
        
        $this->assertTrue($is_1122_scored, '1122 not scored!');
    }
    
    /**
     * 
     */
    public function testCalculateAgregatedPointsFor4124() 
    {
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        // init conts
        // get all replics that change score for behaviour '4124'
        $replicsFor_4124 = Dialogs::model()->findAll('excel_id IN (332, 336)');
        
        $count_0 = 0;
        $count_1 = 0;
        
        // get 4124
        $pointFor_4124 = CharactersPointsTitles::model()->find('code = :code', ['code' => '4124']);  
        
        // init dialog logs
        foreach($replicsFor_4124 as $dialogEntity) {
            LogHelper::setLogDoialogPoint( $dialogEntity->id, $simulation->id, $pointFor_4124->id);
            
            $dialogsPoint = CharactersPoints::model()->find('dialog_id = :dialog_id AND point_id = :point_id',[
                'dialog_id' => $dialogEntity->id,
                'point_id'  => $pointFor_4124->id
            ]);
            
            if ($dialogsPoint->add_value === '1') {
                $count_1++;
            }
            if ($dialogsPoint->add_value === '0') { // not else!
                $count_0++;
            }
        }
        $this->assertEquals(count($replicsFor_4124), ($count_0 + $count_1), 'Wrong replics add_value values!');
        
        // init inbox email from sysadmin
        $emailFromSysadmin = MailBoxModel::model()
            ->find('sim_id = :sim_id AND code = \'M8\'', ['sim_id' => $simulation->id ]);
        $emailFromSysadmin->update('group_id = 1');        
        
        // init MS emails:        
        // MS27 {
        $subject = CommunicationTheme::model()->find(
            'text = :text AND letter_number = :letter_number',[
                'text'          => '!проблема с сервером!',
                'letter_number' => 'MS27'
        ]);
        
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Трутнев
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = $emailFromSysadmin->id;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $ms_27 = MailBoxService::sendMessagePro($sendMailOptions);
        
        $count_0++; // this is 0 point email
        // MS27 }
        
        // MS28 {
        $subject = CommunicationTheme::model()->find(
            'text = :text AND letter_number = :letter_number',[
                'text'          => 'Бюджет производства прошлого года',
                'letter_number' => 'MS28'
        ]);
        
        $attachment = MyDocumentsModel::model()->find(
            'sim_id = :sim_id AND fileName = \'Бюджет производства_01_итог.xlsx\'',[
            'sim_id' => $simulation->id
        ]);
        
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('11'); // Трутнев
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:02';
        $sendMailOptions->messageId  = 0;
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $attachment->id;
        $sendMailOptions->subject_id = $subject->id;
        $ms_28 = MailBoxService::sendMessagePro($sendMailOptions);
        
        $count_1++; // this is 1 point email
        // MS28 }
        
        // MS29 {
        $subject = CommunicationTheme::model()->find(
            'text = :text AND letter_number = :letter_number',[
                'text'          => 'задача: бюджет производства прошлого года',
                'letter_number' => 'MS29'
        ]);
        
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Трутнев
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:03';
        $sendMailOptions->messageId  = 0;
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $ms_29 = MailBoxService::sendMessagePro($sendMailOptions);
        
        $count_0++; // this is 0 point email
        // MS29 }
        
        // logging
        
        $logs = [
            0 => [10, 13, 'activated', 32500],
            1 => [10, 13, 'deactivated', 32600, 4 => ['mailId' => $ms_27->id]],
            2 => [10, 13, 'activated', 32700],
            3 => [10, 13, 'deactivated', 32800, 4 => ['mailId' => $ms_28->id]],
            4 => [10, 13, 'activated', 32900],
            5 => [10, 13, 'deactivated', 33000, 4 => ['mailId' => $ms_29->id]],
        ];
        
        $event = new EventsManager();
        $event->getState($simulation, $logs);
        
        // calculate point total scores
        SimulationService::saveAgregatedPoints($simulation->id);
        
        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);
        
        // assertions:        
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');
        
        $is_4124_scored = false;
        
        foreach ($assessments as $assessment) {  
            if ($assessment->point->code === '4124') {
                // check 4124 is rigth
                // floor because 8.3333 is not equals to 8.3333333333333 in phpunit! :)
                $this->assertEquals(
                    floor($assessment->value*1000), 
                    floor(($count_1/($count_0 + $count_1))*$assessment->point->scale*1000), 
                    'Wrong 4124 value'
               );
               $is_4124_scored = true;
            } else {
                // check outer points is 0
                if (in_array($assessment->point->code, ['4135', '341c1'])) { continue; }
                $this->assertEquals($assessment->value, 0, 'Wrong not acted behaviour value for '.$assessment->point->code);
            }
        }
        
        $this->assertTrue($is_4124_scored, '4124 not scored!');
    } 
}

