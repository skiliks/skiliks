<?php

/**
 *
 * @author slavka
 */
class SimulationServiceTest extends CDbTestCase
{
    /**
     * Проверяет что в результат запуска чимуляции:
     * 1. Проверяет что инициализируются флаги
     */
    public function testSimulationStart()
    {
        $this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);

        $simulationFlags = SimulationFlagsModel::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        $this->assertTrue(count($simulationFlags) > 0);

        foreach ($simulationFlags as $simulationFlag) {
            $this->assertEquals(0, $simulationFlag->value);
        }
    }

    /**
     * Проверяет правильность оценивания игрока по за поведение 1122 
     * (оценивание обычным способом, лог писем пуст) 
     * оценка = максимальный_балл * (количество_правильных_проявления / количество_проявления_по_поведения_в_целом)
     */
    public function testCalculateAgregatedPointsFor1122() 
    {
        $this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        // init conts
        // get all replics that change score for behaviour '1122'
        $replicsFor_1122 = Dialog::model()->findAll('excel_id IN (210, 214, 311, 323, 424, 710, 714, 715, 766, 770, 211, 213, 235, 312, 322, 423, 521, 653, 656, 711, 713, 767, 769, 771)');
        
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
     * Проверяет правильность оценки по 4124
     */
    public function testCalculateAgregatedPointsFor4124() 
    {
        $this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        // init conts
        // get all replics that change score for behaviour '4124'
        $replicsFor_4124 = Dialog::model()->findAll('excel_id IN (332, 336)');
        
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
            0 => [10, 13, 'activated', 32500, 'window_uid' => 1],
            1 => [10, 13, 'deactivated', 32600, 'window_uid' => 1, 4 => ['mailId' => $ms_27->id]],
            2 => [10, 13, 'activated', 32700, 'window_uid' => 2],
            3 => [10, 13, 'deactivated', 32800, 'window_uid' => 2, 4 => ['mailId' => $ms_28->id]],
            4 => [10, 13, 'activated', 32900, 'window_uid' => 3],
            5 => [10, 13, 'deactivated', 33000, 'window_uid' => 3, 4 => ['mailId' => $ms_29->id]],
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

    /**
     * Проверяет склейку детального логировнания в агрегированное:
     * 1. Если суммарная активность по activity превышает 10 реальных секунд
     * - то все записи по activity логруются отдельными строками агрегированного лога
     * 2. Если суммарная активность по activity НЕ превышает 10 реальных секунд
     * - то все записи по activity склаиваются с предыдущим activity
     * 3. Проверяет особенность для mainWindows [main screen,mail main,phone main,documents main]
     * 4. Проверяет особенность для суммирования работа с письмами
     *     (а то правило для mail main сильно фрагментирует работу с почтой)
     */
    public function testActionsAgregationMechanism()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);

        $time = 32000;
        $speedFactor = Yii::app()->params['public']['skiliksSpeedFactor'];

        $email1 = MailBoxModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'group_id' => MailBoxModel::INBOX_FOLDER_ID,
            'code'     => 'MY1'
        ]);
        $email2 = MailBoxModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'group_id' => MailBoxModel::INBOX_FOLDER_ID,
            'code'     => 'MY2'
        ]);
        $email3 = MailBoxModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'group_id' => MailBoxModel::INBOX_FOLDER_ID,
            'code'     => 'MY3'
        ]);

        $n = 10;
        for ($i = 0; $i < $n; $i++) {
            // add set of short by time user-actions {
            $logs = [
                0  => [10, 11, 'activated'  , $time     , ['mailId' => $email1->id], 'window_uid' => 100],
                1  => [10, 11, 'deactivated', $time + 10, ['mailId' => $email1->id], 'window_uid' => 100],
                2  => [3 , 3 , 'activated'  , $time + 10,                            'window_uid' => 200],
                3  => [3 , 3 , 'deactivated', $time + 20,                            'window_uid' => 200],
                4  => [1 , 1 , 'activated'  , $time + 20,                            'window_uid' => 400],
                5  => [1 , 1 , 'deactivated', $time + 30,                            'window_uid' => 400],
                6  => [20, 21, 'activated'  , $time + 30,                            'window_uid' => 500],
                7  => [20, 21, 'deactivated', $time + 40,                            'window_uid' => 500],
                8  => [40, 41, 'activated'  , $time + 40,                            'window_uid' => 600],
                9  => [40, 41, 'deactivated', $time + 50,                            'window_uid' => 600],

                10 => [10, 11, 'activated'  , $time + 50,                   ['mailId' => $email2->id], 'window_uid' => 100],
                11 => [10, 11, 'deactivated', $time + 50 + $speedFactor*11, ['mailId' => $email2->id], 'window_uid' => 100],
            ];

            $time = $time + 50 + $speedFactor*11;

            $event = new EventsManager();
            $event->getState($simulation, $logs);
            // add set of short by time user-actions }

            // add short by time user-action {
            if (2 == $i) {
                $logs = [
                    0 => [40, 41, 'activated'  , $time                        , 'window_uid' => 300],
                    1 => [40, 41, 'deactivated', $time + round($speedFactor/2), 'window_uid' => 300],
                    2 => [1 , 1 , 'activated'  , $time + round($speedFactor/2), 'window_uid' => 400],

                    // make duration more than 10 real seconds
                    3 => [1 , 1 , 'deactivated', $time + $speedFactor*11      , 'window_uid' => 400],

                    4 => [10, 11, 'activated'  , $time + $speedFactor*11,      ['mailId' => $email3->id], 'window_uid' => 100],
                    5 => [10, 11, 'deactivated', $time + 10 + $speedFactor*11, ['mailId' => $email3->id], 'window_uid' => 100],
                ];

                $event = new EventsManager();
                $event->getState($simulation, $logs);

                $time = $time + 10 + $speedFactor*11;

            }
            // add short by time user-action }
        }

        LogHelper::combineLogActivityAgregated($simulation);

        $agregatedLogs = LogActivityActionAgregated::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        $res = [
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28'],
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28'],
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'], // short activity aggregated
            ['action' => 'MY2'        , 'duration' => '00:01:32'],
            ['action' => 'main screen', 'duration' => '00:01:34'], // long activity stand alone
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28'],
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28'],
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28'],
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28'],
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28'],
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28'],
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:01:28']
        ];

        $this->assertEquals(count($res), count($agregatedLogs), 'Total');

        $j = 0;
        foreach ($agregatedLogs as $agregatedLog) {
            //echo "\n", $agregatedLog->leg_action, ' :: ', $agregatedLog->duration;
            $this->assertEquals($res[$j]['action'],   $agregatedLog->leg_action, 'type, iteration '.$j);
            $this->assertEquals($res[$j]['duration'], $agregatedLog->duration,  'duration, iteration '.$j);
            $j++;
        }
    }
}

