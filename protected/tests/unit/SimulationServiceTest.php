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
        //$this->markTestSkipped();

        // init simulation
        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(Simulation::TYPE_PROMOTION, $user);

        $simulationFlags = SimulationFlag::model()->findAllByAttributes(['sim_id' => $simulation->id]);

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
        //$this->markTestSkipped();

        // init simulation
        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(Simulation::TYPE_PROMOTION, $user);
        
        // init conts
        // get all replics that change score for behaviour '1122'
        $replicsFor_1122 = Replica::model()->findAll('excel_id IN (210, 214, 311, 323, 424, 710, 714, 715, 766, 770, 211, 213, 235, 312, 322, 423, 521, 653, 656, 711, 713, 767, 769, 771)');
        
        $count_0 = 0;
        $count_1 = 0;
        
        // get 1122
        $pointFor_1122 = CharactersPointsTitles::model()->find('code = :code', ['code' => '1122']);  
        $this->assertNotNull($pointFor_1122);
        // init logs
        foreach($replicsFor_1122 as $dialogEntity) {
            LogHelper::setLogDoialogPoint( $dialogEntity->id, $simulation->id, $pointFor_1122->id);
            
            $dialogsPoint = CharactersPoints::model()->find('dialog_id = :dialog_id AND point_id = :point_id',[
                'dialog_id' => $dialogEntity->id,
                'point_id'  => $pointFor_1122->id
            ]);
            $this->assertNotNull($dialogsPoint);
            
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
                // check 1122 is right
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
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);
        
        // init conts
        // get all replics that change score for behaviour '4124'
        $replicsFor_4124 = Replica::model()->findAll('excel_id IN (332, 336)');
        
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
        $emailFromSysadmin = MailBoxService::copyMessageFromTemplateByCode($simulation->id, 'M8');

        // init MS emails:        
        // MS27 {
        $ms_27 = LibSendMs::sendMs27_w($simulation);
        $count_0++; // this is 0 point email
        // MS27 }
        
        // MS28 {
        $ms_28 = LibSendMs::sendMs28_r($simulation);
        $count_1++; // this is 1 point email
        // MS28 }
        
        // MS29 {
        $ms_29 = LibSendMs::sendMs29_r($simulation);
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
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

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

    public function testActionsAgregationMechanism_2()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        $data['data'] = [];

        $action1 = ActivityAction::model()->findByAttributes([
            'activity_id' => 'A_wait',
            'window_id'   => 1
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Window',
            'leg_action'            => 'main screen',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => NULL,
            'doc_code'              => NULL,
            'window_id'             => 1,
            'subtype'               => 'main screen',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => NULL,
            'coincidence_mail_code' => NULL,
            'category'              => 5,
            'is_keep_last_category' => 0,
            'start_time'            => '09:00:00',
            'end_time'              => '09:07:19',
            'diff_time'             => '00:07:19',
            'activity_action_id'    => $action1->id,
            'category_id'           => NULL,
            'activity_id'           => 'A_wait',
            'window_uid'            => 100,
        ];

        $action41 = ActivityAction::model()->findByAttributes([
            'activity_id' => 'A_wait',
            'window_id'   => 41
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Window',
            'leg_action'            => 'documents main',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => NULL,
            'doc_code'              => NULL,
            'window_id'             => 41,
            'subtype'               => 'documents main',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => NULL,
            'coincidence_mail_code' => NULL,
            'category'              => 5,
            'is_keep_last_category' => 0,
            'start_time'            => '09:07:20',
            'end_time'              => '09:08:03',
            'diff_time'             => '00:00:43',
            'activity_action_id'    => $action41->id,
            'category_id'           => NULL,
            'activity_id'           => 'A_wait',
            'window_uid'            => 101,
        ];

        $actionTRS6 = ActivityAction::model()->findByAttributes([
            'activity_id' => 'TRS6',
            'mail_id'     => NULL
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Documents_leg',
            'leg_action'            => 'D1',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => NULL,
            'doc_code'              => 'D1',
            'window_id'             => 42,
            'subtype'               => '',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => NULL,
            'coincidence_mail_code' => NULL,
            'category'              => '2_min',
            'is_keep_last_category' => 0,
            'start_time'            => '09:08:03',
            'end_time'              => '09:11:58',
            'diff_time'             => '00:03:55',
            'activity_action_id'    => $actionTRS6->id,
            'category_id'           => NULL,
            'activity_id'           => 'TRS6',
            'window_uid'            => 102,
        ];

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Window',
            'leg_action'            => 'documents main',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => NULL,
            'doc_code'              => NULL,
            'window_id'             => 41,
            'subtype'               => 'documents main',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => NULL,
            'coincidence_mail_code' => NULL,
            'category'              => 5,
            'is_keep_last_category' => 0,
            'start_time'            => '09:11:58',
            'end_time'              => '09:12:41',
            'diff_time'             => '00:00:43',
            'activity_action_id'    => $action41->id,
            'category_id'           => NULL,
            'activity_id'           => 'A_wait',
            'window_uid'            => 103,
        ];

        $action21 = ActivityAction::model()->findByAttributes([
            'activity_id' => 'A_wait',
            'window_id'   => 21
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Window',
            'leg_action'            => 'mail main',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => NULL,
            'doc_code'              => NULL,
            'window_id'             => 11,
            'subtype'               => 'mail main',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => NULL,
            'coincidence_mail_code' => NULL,
            'category'              => 5,
            'is_keep_last_category' => 0,
            'start_time'            => '09:12:41',
            'end_time'              => '09:12:50',
            'diff_time'             => '00:00:09',
            'activity_action_id'    => $action21->id,
            'category_id'           => NULL,
            'activity_id'           => 'A_wait',
            'window_uid'            => 104,
        ];

        $actionAMY1 = ActivityAction::model()->findByAttributes([
            'activity_id' => 'AMY1',
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Inbox_leg',
            'leg_action'            => '',
            'mail_id'               => 1,
            'dialog_code'           => NULL,
            'mail_code'             => 'MY1',
            'doc_code'              => NULL,
            'window_id'             => 11,
            'subtype'               => '',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => 1,
            'coincidence_mail_code' => 'MY1',
            'category'              => 3,
            'is_keep_last_category' => 0,
            'start_time'            => '09:12:50',
            'end_time'              => '09:13:03',
            'diff_time'             => '00:00:13',
            'activity_action_id'    => $actionAMY1->id,
            'category_id'           => NULL,
            'activity_id'           => 'AMY1',
            'window_uid'            => 104,
        ];

        $actionTRS6m = ActivityAction::model()->findByAttributes([
            'activity_id' => 'TRS6',
            'document_id' => NULL
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Outbox_leg',
            'leg_action'            => 'MS48',
            'mail_id'               => 2,
            'dialog_code'           => NULL,
            'mail_code'             => 'MS48',
            'doc_code'              => NULL,
            'window_id'             => 13,
            'subtype'               => '',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => 3,
            'coincidence_mail_code' => 'MS48',
            'category'              => '2_min',
            'is_keep_last_category' => 0,
            'start_time'            => '09:13:03',
            'end_time'              => '09:14:49',
            'diff_time'             => '00:01:46',
            'activity_action_id'    => $actionTRS6m->id,
            'category_id'           => NULL,
            'activity_id'           => 'TRS6',
            'window_uid'            => 106,
        ];

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Inbox_leg',
            'leg_action'            => 'MY1',
            'mail_id'               => 1,
            'dialog_code'           => NULL,
            'mail_code'             => 'MY1',
            'doc_code'              => NULL,
            'window_id'             => 11,
            'subtype'               => '',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => 1,
            'coincidence_mail_code' => 'MY1',
            'category'              => 3,
            'is_keep_last_category' => 0,
            'start_time'            => '09:14:49',
            'end_time'              => '09:15:00',
            'diff_time'             => '00:00:11',
            'activity_action_id'    => $actionAMY1->id,
            'category_id'           => NULL,
            'activity_id'           => 'AMY1',
            'window_uid'            => 104,
        ];

        $actionAMSY10 = ActivityAction::model()->findByAttributes([
            'activity_id' => 'AMSY10',
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Outbox_leg',
            'leg_action'            => 'MSY10',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => 'MSY10',
            'doc_code'              => NULL,
            'window_id'             => 11,
            'subtype'               => '',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => 3,
            'coincidence_mail_code' => NULL,
            'category'              => 2,
            'is_keep_last_category' => 0,
            'start_time'            => '09:15:00',
            'end_time'              => '09:15:14',
            'diff_time'             => '00:00:14',
            'activity_action_id'    => $actionAMSY10->id,
            'category_id'           => NULL,
            'activity_id'           => 'AMSY10',
            'window_uid'            => 104,
        ];

        $actionAU = ActivityAction::model()->findByAttributes([
            'activity_id' => 'A_already_used',
            'document_id' => NULL
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Outbox_leg',
            'leg_action'            => 'MS48',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => 'MS48',
            'doc_code'              => NULL,
            'window_id'             => 13,
            'subtype'               => '',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => 3,
            'coincidence_mail_code' => 'MS48',
            'category'              => 5,
            'is_keep_last_category' => 0,
            'start_time'            => '09:15:14',
            'end_time'              => '09:15:43',
            'diff_time'             => '00:00:29',
            'activity_action_id'    => $actionAU->id,
            'category_id'           => NULL,
            'activity_id'           => 'A_already_used',
            'window_uid'            => 104,
        ];

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Window',
            'leg_action'            => 'documents main',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => NULL,
            'doc_code'              => NULL,
            'window_id'             => 41,
            'subtype'               => 'documents main',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => NULL,
            'coincidence_mail_code' => NULL,
            'category'              => 5,
            'is_keep_last_category' => 0,
            'start_time'            => '09:15:43',
            'end_time'              => '09:16:28',
            'diff_time'             => '00:00:45',
            'activity_action_id'    => $action41->id,
            'category_id'           => NULL,
            'activity_id'           => 'A_wait',
            'window_uid'            => 110,
        ];

        $actionT321 = ActivityAction::model()->findByAttributes([
            'activity_id' => 'T3.2.1',
        ]);

        $data['data'][] = [
            'sim_id'                => $simulation->id,
            'leg_type'              => 'Documents_leg',
            'leg_action'            => 'D1',
            'mail_id'               => NULL,
            'dialog_code'           => NULL,
            'mail_code'             => NULL,
            'doc_code'              => 'D1',
            'window_id'             => 42,
            'subtype'               => '',
            'type_of_init'          => NULL,
            'dialog_id'             => NULL,
            'group_id'              => NULL,
            'coincidence_mail_code' => NULL,
            'category'              => 1,
            'is_keep_last_category' => 0,
            'start_time'            => '09:16:29',
            'end_time'              => '09:20:55',
            'diff_time'             => '00:04:26',
            'activity_action_id'    => $actionT321->id,
            'category_id'           => NULL,
            'activity_id'           => 'T3.2.1',
            'window_uid'            => 111,
        ];

        LogHelper::combineLogActivityAgregated($simulation, $data);

        $agregatedLogs = LogActivityActionAgregated::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        $this->assertEquals(4, count($agregatedLogs), 'Total');

        $res = [
            ['action' => 'main screen', 'duration' => '00:08:03'],
            ['action' => 'D1'         , 'duration' => '00:05:00'],
            ['action' => 'MS48'       , 'duration' => '00:03:25'],
            ['action' => 'D1'         , 'duration' => '00:04:26'],
        ];

        $j = 0;
        foreach ($agregatedLogs as $agregatedLog) {
            $this->assertEquals($res[$j]['action'],   $agregatedLog->leg_action, 'type, iteration '.$j);
            $this->assertEquals($res[$j]['duration'], $agregatedLog->duration,   'duration, iteration '.$j);
            $j++;
        }
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 11 W, 15R, 0N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase1()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // init MS emails:
        $ms[] = LibSendMs::sendMs10_w($simulation);
        $ms[] = LibSendMs::sendMs21_w($simulation);
        $ms[] = LibSendMs::sendMs22_w($simulation);
        $ms[] = LibSendMs::sendMs23_w($simulation);
        $ms[] = LibSendMs::sendMs27_w($simulation);
        $ms[] = LibSendMs::sendMs30_w($simulation);
        $ms[] = LibSendMs::sendMs32_w($simulation);
        $ms[] = LibSendMs::sendMs49_w($simulation);
        $ms[] = LibSendMs::sendMs50_w($simulation);
        $ms[] = LibSendMs::sendMs54_w($simulation);
        $ms[] = LibSendMs::sendMs58_w($simulation);

        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs28_r($simulation);
        $ms[] = LibSendMs::sendMs35_r($simulation);
        $ms[] = LibSendMs::sendMs36_r($simulation);
        $ms[] = LibSendMs::sendMs37_r($simulation);
        $ms[] = LibSendMs::sendMs39_r($simulation);
        $ms[] = LibSendMs::sendMs40_r($simulation);
        $ms[] = LibSendMs::sendMs48_r($simulation);
        $ms[] = LibSendMs::sendMs51_r($simulation);
        $ms[] = LibSendMs::sendMs53_r($simulation);
        $ms[] = LibSendMs::sendMs55_r($simulation);
        $ms[] = LibSendMs::sendMs57_r($simulation);
        $ms[] = LibSendMs::sendMs60_r($simulation);
        $ms[] = LibSendMs::sendMs61_r($simulation);
        $ms[] = LibSendMs::sendMs69_r($simulation);

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }

        $event = new EventsManager();
        $event->processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalize($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0 W, 1R, 0N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase2()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // init MS emails:
        $ms[20] = LibSendMs::sendMs20_r($simulation);

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }

        $event = new EventsManager();
        $event->processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalize($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0W, 0R, 0N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase3()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // calculate point total scores
        SimulationService::saveEmailsAnalize($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0 W, 1R - 15 раз, 0N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase4()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // init MS emails:
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs20_r($simulation);

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }

        $event = new EventsManager();
        $event->processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalize($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0W, 14R, 0N => 2 балла
     */
    public function testCalculateAgregatedPointsFor3326_2pointsCase1()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // init MS emails:
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs28_r($simulation);
        $ms[] = LibSendMs::sendMs35_r($simulation);
        $ms[] = LibSendMs::sendMs36_r($simulation);
        $ms[] = LibSendMs::sendMs37_r($simulation);
        $ms[] = LibSendMs::sendMs39_r($simulation);
        $ms[] = LibSendMs::sendMs48_r($simulation);
        $ms[] = LibSendMs::sendMs51_r($simulation);
        $ms[] = LibSendMs::sendMs53_r($simulation);
        $ms[] = LibSendMs::sendMs55_r($simulation);
        $ms[] = LibSendMs::sendMs57_r($simulation);
        $ms[] = LibSendMs::sendMs60_r($simulation);
        $ms[] = LibSendMs::sendMs61_r($simulation);
        $ms[] = LibSendMs::sendMs69_r($simulation);

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }

        $event = new EventsManager();
        $event->processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalize($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(2, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 3W, 14R, 0N => 1 балл
     */
    public function testCalculateAgregatedPointsFor3326_1pointsCase1()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // init MS emails:
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs28_r($simulation);
        $ms[] = LibSendMs::sendMs35_r($simulation);
        $ms[] = LibSendMs::sendMs36_r($simulation);
        $ms[] = LibSendMs::sendMs37_r($simulation);
        $ms[] = LibSendMs::sendMs39_r($simulation);
        $ms[] = LibSendMs::sendMs48_r($simulation);
        $ms[] = LibSendMs::sendMs51_r($simulation);
        $ms[] = LibSendMs::sendMs53_r($simulation);
        $ms[] = LibSendMs::sendMs55_r($simulation);
        $ms[] = LibSendMs::sendMs57_r($simulation);
        $ms[] = LibSendMs::sendMs60_r($simulation);
        $ms[] = LibSendMs::sendMs61_r($simulation);
        $ms[] = LibSendMs::sendMs69_r($simulation);

        $ms[] = LibSendMs::sendMs50_w($simulation);
        $ms[] = LibSendMs::sendMs54_w($simulation);
        $ms[] = LibSendMs::sendMs58_w($simulation);

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }

        $event = new EventsManager();
        $event->processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalize($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(1, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0W, 13R, 0N (total R = 13) => 2 балла
     */
    public function testCalculateAgregatedPointsFor3326_2pointsCase2()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // init MS emails:
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs28_r($simulation);
        $ms[] = LibSendMs::sendMs35_r($simulation);
        $ms[] = LibSendMs::sendMs36_r($simulation);
        $ms[] = LibSendMs::sendMs37_r($simulation);
        $ms[] = LibSendMs::sendMs39_r($simulation);
        $ms[] = LibSendMs::sendMs48_r($simulation);
        $ms[] = LibSendMs::sendMs51_r($simulation);
        $ms[] = LibSendMs::sendMs53_r($simulation);
        $ms[] = LibSendMs::sendMs55_r($simulation);
        $ms[] = LibSendMs::sendMs57_r($simulation);
        $ms[] = LibSendMs::sendMs60_r($simulation);
        $ms[] = LibSendMs::sendMs61_r($simulation);

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }

        $event = new EventsManager();
        $event->processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalize($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);

        // assertions:
        $this->assertNotEquals(count($assessments), 1, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(2, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0W, 12R, 1N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase5()
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // init MS emails:
        $ms[] = LibSendMs::sendMs20_r($simulation);
        $ms[] = LibSendMs::sendMs28_r($simulation);
        $ms[] = LibSendMs::sendMs35_r($simulation);
        $ms[] = LibSendMs::sendMs36_r($simulation);
        $ms[] = LibSendMs::sendMs37_r($simulation);
        $ms[] = LibSendMs::sendMs39_r($simulation);
        $ms[] = LibSendMs::sendMs48_r($simulation);
        $ms[] = LibSendMs::sendMs51_r($simulation);
        $ms[] = LibSendMs::sendMs53_r($simulation);
        $ms[] = LibSendMs::sendMs55_r($simulation);
        $ms[] = LibSendMs::sendMs57_r($simulation);
        $ms[] = LibSendMs::sendMs60_r($simulation);
        $ms[] = LibSendMs::sendMs79_n($simulation);

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }

        $event = new EventsManager();
        $event->processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalize($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);

        // assertions:
        $this->assertNotEquals(count($assessments), 1, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }
}

