<?php

/**
 *
 * @author slavka
 */
class SimulationServiceUnitTest extends CDbTestCase
{
    use UnitLoggingTrait;
    /**
     * Проверяет что в результат запуска чимуляции:
     * 1. Проверяет что инициализируются флаги
     */
    public function testSimulationStart()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $simulationFlags = SimulationFlag::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        $this->assertTrue(count($simulationFlags) > 0);

        foreach ($simulationFlags as $simulationFlag) {
            $this->assertEquals(0, $simulationFlag->value);
        }
    }

    /**
     * Проверяет что после установки симуляции на паузу и последующего возобновления
     * время не изменяется
     */
    public function testSimulationPause()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_LITE;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $awaiting = 2; //sec

        $before = $simulation->getGameTime();
        SimulationService::pause($simulation);
        sleep($awaiting);
        SimulationService::resume($simulation);
        $after = $simulation->getGameTime();

        $this->assertEquals(substr($before, 0, 5), substr($after, 0, 5));
        $this->assertEquals($awaiting, $simulation->skipped);
    }

    /**
     * Проверяет что lite симуляция стартует и останавливается без ошибок
     * время не изменяется
     */
    public function testLiteSimulationStop()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_LITE;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        DayPlanService::copyPlanToLog($simulation, 660);
        SimulationService::simulationStop($simulation);
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
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init conts
        // get all replics that change score for behaviour '1122'
        $criteria = new CDbCriteria();
        $criteria->addInCondition('excel_id', [210, 214, 311, 323, 424, 710, 714, 715, 766, 770, 211, 213, 235, 312, 322, 423, 521, 653, 656, 711, 713, 767, 769, 771]);
        $replicsFor_1122 = $simulation->game_type->getReplicas($criteria);
        
        $count_0 = 0;
        $count_1 = 0;
        
        // get 1122
        $pointFor_1122 = $simulation->game_type->getHeroBehaviour(['code' => '1122']);
        $this->assertNotNull($pointFor_1122);
        // init logs
        foreach($replicsFor_1122 as $dialogEntity) {
            $dialogsPoint = ReplicaPoint::model()->find('dialog_id = :dialog_id AND point_id = :point_id',[
                'dialog_id' => $dialogEntity->id,
                'point_id'  => $pointFor_1122->id
            ]);
            $this->assertNotNull($dialogsPoint);

            LogHelper::setDialogPoint( $dialogEntity->id, $simulation->id, $dialogsPoint);
            
            if ($dialogsPoint->add_value === '1') {
                $count_1++;
            }
            if ($dialogsPoint->add_value === '0') { // not else!
                $count_0++;
            }
        }
        
        // calculate point total scores
        SimulationService::saveAggregatedPoints($simulation->id);
        
        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);
        
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
    public function testCalculateAggregatedPointsFor4124()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // init conts
        // get all replics that change score for behaviour '4124'
        $replicsFor_4124 = Replica::model()->findAll('excel_id IN (332, 336)');
        
        $count_0 = 0;
        $count_1 = 0;
        
        // get 4124
        $pointFor_4124 = HeroBehaviour::model()->find('code = :code', ['code' => '4124']);
        
        // init dialog logs
        foreach ($replicsFor_4124 as $dialogEntity) {
            $dialogsPoint = ReplicaPoint::model()->find('dialog_id = :dialog_id AND point_id = :point_id',[
                'dialog_id' => $dialogEntity->id,
                'point_id'  => $pointFor_4124->id
            ]);

            LogHelper::setDialogPoint($dialogEntity->id, $simulation->id, $dialogsPoint);
            
            if ($dialogsPoint->add_value === '1') {
                $count_1++;
            }
            if ($dialogsPoint->add_value === '0') { // not else!
                $count_0++;
            }
        }
        $this->assertEquals(count($replicsFor_4124), ($count_0 + $count_1), 'Wrong replics add_value values!');

        // init MS emails:        
        // MS27 {
        $ms_27 = LibSendMs::sendMs($simulation, 'MS27');
        $count_0++; // this is 0 point email
        // MS27 }
        
        // MS28 {
        $ms_28 = LibSendMs::sendMs($simulation, 'MS28');
        $count_1++; // this is 1 point email
        // MS28 }
        
        // MS29 {
        $ms_29 = LibSendMs::sendMs($simulation, 'MS29');
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
        
        EventsManager::getState($simulation, $logs);
        
        // calculate point total scores
        SimulationService::saveAggregatedPoints($simulation->id);
        
        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);
        
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
                if (in_array($assessment->point->code, ['4135', '341c1', '4145'])) { continue; }
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
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $time = 32000;
        $speedFactor = $simulation->getSpeedFactor();

        $email1 = MailBox::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'group_id' => MailBox::FOLDER_INBOX_ID,
            'code'     => 'MY1'
        ]);
        $email2 = MailBox::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'group_id' => MailBox::FOLDER_INBOX_ID,
            'code'     => 'MY2'
        ]);
        $email3 = MailBox::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'group_id' => MailBox::FOLDER_INBOX_ID,
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
                11 => [10, 11, 'deactivated', $time + 50 + $speedFactor*22, ['mailId' => $email2->id], 'window_uid' => 100],
            ];

            $time = $time + 50 + $speedFactor*22;

            EventsManager::getState($simulation, $logs);
            // add set of short by time user-actions }

            // add short by time user-action {
            if (2 == $i) {
                $logs = [
                    0 => [40, 41, 'activated'  , $time               , 'window_uid' => 300],
                    1 => [40, 41, 'deactivated', $time + $speedFactor, 'window_uid' => 300],
                    2 => [1 , 1 , 'activated'  , $time + $speedFactor, 'window_uid' => 400],

                    // make duration more than 10 real seconds
                    3 => [1 , 1 , 'deactivated', $time + $speedFactor*22      , 'window_uid' => 400],

                    4 => [10, 11, 'activated'  , $time + $speedFactor*22,      ['mailId' => $email3->id], 'window_uid' => 100],
                    5 => [10, 11, 'deactivated', $time + 10 + $speedFactor*22, ['mailId' => $email3->id], 'window_uid' => 100],
                ];

                EventsManager::getState($simulation, $logs);

                $time = $time + 10 + $speedFactor*22;

            }
            // add short by time user-action }
        }

        LogHelper::combineLogActivityAgregated($simulation);

        $aggregatedLogs = LogActivityActionAgregated::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        // speedFactor : 6;
        $res = [
            ['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:22'],
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:22'],
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'], // short activity aggregated
            ['action' => 'MY2'        , 'duration' => '00:02:18'],
            ['action' => 'main screen', 'duration' => '00:02:26'], // long activity stand alone
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:22'],
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:22'],
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:22'],
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:22'],
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:22'],
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:22'],
            //['action' => 'MY1'        , 'duration' => '00:00:10'],
            ['action' => 'plan'       , 'duration' => '00:00:40'],
            ['action' => 'MY2'        , 'duration' => '00:02:12']
        ];

        if (8 == $speedFactor) {
            $res = [
                ['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:03:06'],
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:03:06'],
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'], // short activity aggregated
                ['action' => 'MY2'        , 'duration' => '00:03:04'],
                ['action' => 'main screen', 'duration' => '00:03:08'], // long activity stand alone
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:03:06'],
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:03:06'],
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:03:06'],
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:03:06'],
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:03:06'],
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:03:06'],
                //['action' => 'MY1'        , 'duration' => '00:00:10'],
                ['action' => 'plan'       , 'duration' => '00:00:40'],
                ['action' => 'MY2'        , 'duration' => '00:02:56']
            ];
        }

        $j = 0;
        foreach ($aggregatedLogs as $aggregatedLog) {
            // echo "\n", $aggregatedLog->leg_action, ' :: ', $aggregatedLog->duration;
            $this->assertEquals($res[$j]['action'],   $aggregatedLog->leg_action, 'type, iteration '.$j);
            $this->assertEquals($res[$j]['duration'], $aggregatedLog->duration,  'duration, iteration '.$j);
            $j++;
        }
        $this->assertEquals(count($res), count($aggregatedLogs), 'Total');
    }

    public function testActionsAgregationMechanism_2()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite                 = new Invite();
        $invite->scenario       = new Scenario();
        $invite->receiverUser   = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation             = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $data = [];

        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $action1 = ActivityAction::model()->findByAttributes([
            'activity_id' => $activity->getPrimaryKey(),
            'window_id'   => 1
        ]);

        $log = $data[]           = new LogActivityAction();
        $log->sim_id             = $simulation->id;
        $log->start_time         = '09:00:00';
        $log->end_time           = '09:07:19';
        $log->window             = 1;
        $log->activity_action_id = $action1->id;
        $log->window_uid         = 100;

        $action41 = ActivityAction::model()->findByAttributes([
            'activity_id' => $activity->getPrimaryKey(),
            'window_id'   => 41
        ]);

        $log = $data[]   = new LogActivityAction();
        $log->sim_id     = $simulation->id;
        $log->start_time = '09:07:40';
        $log->end_time   = '09:08:03';
        $log->window     = 41;
        $log->window_uid = 101;

        $log->activity_action_id = $action41->primaryKey;

        $actionTRS5 = ActivityAction::model()->findByAttributes([
            'activity_id' => $simulation->game_type->getActivity(['code' => 'ARS5'])->getPrimaryKey(),
            'mail_id'     => NULL
        ]);

        $log = $data[] = new LogActivityAction();
        $log->sim_id             = $simulation->id;
        $log->activity_action_id = $actionTRS5->primaryKey;
        $log->start_time         = '09:08:03';
        $log->end_time           = '09:12:18';
        $log->window_uid         = 102;


        $log = $data[] = new LogActivityAction();
        $log->sim_id             = $simulation->id;
        $log->window             = 41;
        $log->start_time         = '09:12:18';
        $log->end_time           = '09:12:41';
        $log->activity_action_id = $action41->primaryKey;

        $action21 = ActivityAction::model()->findByAttributes([
            'activity_id' => $simulation->game_type->getActivity(['code' => 'A_wait'])->id,
            'window_id'   => 21
        ]);

        $log = $data[] = new LogActivityAction();
        $log->sim_id             = $simulation->id;
        $log->window             = 11;
        $log->start_time         = '09:12:41';
        $log->end_time           = '09:12:50';
        $log->activity_action_id = $action21->id;

        $actionAMY1 = ActivityAction::model()->findByAttributes([
            'activity_id' => $simulation->game_type->getActivity(['code' => 'AMY1'])->id,
        ]);

        $actionARS7 = ActivityAction::model()->findByAttributes([
            'activity_id' => $simulation->game_type->getActivity(['code' => 'ARS7'])->id,
        ]);

        $log = $data[] = new LogActivityAction();
        $log->sim_id = $simulation->id;
        $log->activity_action_id    = $actionARS7->primaryKey;
        $log->window_uid            = 104;
        $log->start_time            = '09:12:50';
        $log->end_time              = '09:13:03';  // ++
        $actionTRS7m = ActivityAction::model()->findByAttributes([
            'activity_id' => $simulation->game_type->getActivity(['code' => 'ARS7'])->id,
            'document_id' => NULL
        ]);

        $log = $data[] = new LogActivityAction();
        $log->sim_id                = $simulation->id;
        $log->mail_id               = 2;
        $log->start_time            = '09:13:03';
        $log->end_time              = '09:14:59';
        $log->activity_action_id    = $actionTRS7m->id;
        $log->window_uid            = 106;


        $log = $data[] = new LogActivityAction();
        $log->sim_id = $simulation->id;
        $log->mail_id               = 1;
        $log->start_time            = '09:14:59';
        $log->end_time              = '09:15:00';
        $log->activity_action_id    = $actionAMY1->id;
        $log->window_uid = 104;

        $ARS10Activity = $simulation->game_type->getActivity(['code' => 'ARS10']);
        $this->assertNotNull($ARS10Activity);
        $actionARS10 = ActivityAction::model()->findByAttributes([
            'activity_id' => $ARS10Activity->id,
        ]);

        $log = $data[] = new LogActivityAction();

        $log->sim_id                = $simulation->id;
        $log->window                = 11;
        $log->start_time            = '09:15:00';
        $log->end_time              = '09:15:24';
        $log->activity_action_id    = $actionARS10->id;
        $log->window_uid = 104;

        $actionAU = ActivityAction::model()->findByAttributes([
            'activity_id' => $simulation->game_type->getActivity(['code' => 'A_already_used'])->id,
            'document_id' => NULL
        ]);

        $log = $data[] = new LogActivityAction();
        $log->sim_id                = $simulation->id;
        $log->window                = 13;
        $log->start_time            = '09:15:24';
        $log->end_time              = '09:15:39';
        $log->activity_action_id    = $actionAU->id;
        $log->window_uid            = 103;

        $log = $data[]              = new LogActivityAction();
        $log->sim_id                = $simulation->id;
        $log->window                = 41;
        $log->start_time            = '09:15:39';
        $log->end_time              = '09:15:58';
        $log->activity_action_id    = $action41->id;
        $log->window_uid = 110;
        $actionT321 = ActivityAction::model()->findByAttributes([
            'activity_id' => $simulation->game_type->getActivity(['code' => 'T3.2.1'])->id,
            'document_id' => $simulation->game_type->getDocumentTemplate(['code' => 'D1'])->primaryKey
        ]);

        $log = $data[] = new LogActivityAction();
        $log->sim_id                = $simulation->id;
        $log->window                 = 42;
        $log->start_time            = '09:15:59';
        $log->end_time              = '09:20:55';
        $log->activity_action_id    = $actionT321->id;
        $log->window_uid = 111;

        LogHelper::combineLogActivityAgregated($simulation, $data);

        $aggregatedLogs = LogActivityActionAgregated::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        $res = [
            ['action' => 'main screen', 'duration' => '00:08:03'],
            ['action' => 'D1'         , 'duration' => '00:05:00'],
            ['action' => 'MS48'       , 'duration' => '00:02:55'],
            ['action' => 'D1'         , 'duration' => '00:04:56'],
        ];

        $j = 0;
        foreach ($aggregatedLogs as $aggregatedLog) {
//            echo "\n", $aggregatedLog->leg_action, ' :: ', $aggregatedLog->duration;
//            $this->assertEquals($res[$j]['action'],   $aggregatedLog->leg_action, 'type, iteration '.$j);
//            $this->assertEquals($res[$j]['duration'], $aggregatedLog->duration,   'duration, iteration '.$j);
            $j++;
        }
        $this->assertEquals(4, count($aggregatedLogs), 'Total');

    }

    public function testSimulationPerformanceRules()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $eventsManager = new EventsManager();

        // Action for rule id 1
        $first = Replica::model()->byExcelId(516)->find();
        $last = Replica::model()->byExcelId(523)->find();
        $dialogLog = [
            [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32401, 'window_uid' => 1],
            [20, 23, 'activated', 32401, ['dialogId' => $first->id], 'window_uid' => 2],
            [20, 23, 'deactivated', 32460, ['dialogId' => $first->id, 'lastDialogId' => $last->id], 'window_uid' => 2],
            [1, 1, 'activated', 32460, 'window_uid' => 1]
        ];
        $eventsManager->processLogs($simulation, $dialogLog);
        // end rule 1

        // Actions for rule id 5 (AND operation)
        $mail = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M10');
        $mailLog = [
            [1, 1, 'deactivated', 32470, 'window_uid' => 1],
            [10, 11, 'activated', 32470, ['mailId' => $mail->id], 'window_uid' => 3],
            [10, 11, 'deactivated', 32480, ['mailId' => $mail->id], 'window_uid' => 3],
            [1, 1, 'activated', 32480, 'window_uid' => 1]
        ];
        $eventsManager->processLogs($simulation, $mailLog);
        LibSendMs::sendMsByCode($simulation, 'MS83', 32500, 1,1,1);
        // End rule 5

        // Actions for rule id 8 (OR operation)
        LibSendMs::sendMsByCode($simulation, 'MS39', 32600, 1,1,1);
        // End rule 8

        // Alternative action for rule id 8
        $first = $simulation->game_type->getReplica(['excel_id' => 549]);
        $last = Replica::model()->byExcelId(560)->find();
        $dialogLog = [
            [1, 1, 'deactivated', 32610, 'window_uid' => 1],
            [20, 23, 'activated', 32610, ['dialogId' => $first->id], 'window_uid' => 4],
            [20, 23, 'deactivated', 32700, ['dialogId' => $first->id, 'lastDialogId' => $last->id], 'window_uid' => 4],
            [1, 1, 'activated', 32700, 'window_uid' => 1]
        ];
        $eventsManager->processLogs($simulation, $dialogLog);
        // end alt rule 8

        $windowLog = [
            [1, 1, 'deactivated', 35000, 'window_uid' => 1]
        ];
        $eventsManager->processLogs($simulation, $windowLog);

        SimulationService::simulationStop($simulation);

        $executedRules = PerformancePoint::model()->bySimId($simulation->id)->findAll();
        $list = array_map(function($rule) {
            return $rule->performanceRule->code;
        }, $executedRules);
        sort($list);

        $this->assertEquals([1, 5, 8], $list);
    }

    /**
     * Проверяет персональную шкалу
     */
    public function testAssessmentAggregation()
    {
        $this->markTestSkipped();

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $mgr = new EventsManager();
        $scaleTypes = [1 => 'positive', 2 => 'negative', 3 => 'personal'];

        // 3:   ET1.1, step 1, replica 2
        // 7:   E1,    step 1, replica 3
        // 11:  E1,    step 2, replica 2
        // 17:  S1.1,  step 1, replica 1
        // 842: RST3.1,  step 1, replica 1
        // 664: RS3,   step 1, replica 3
        // 667: RS3,   step 2, replica 3
        // 670: RS3,   step 3, replica 3
        $replicas = [3, 7, 11, 17, 842, 664, 667, 670];

        $logs = [];
        $details = [];
        $aggregatedCalculated = [];
        $delta = [];

        foreach ($replicas as $replica) {
            $replica = $simulation->game_type->getReplica(['excel_id' => $replica]);
            $points = ReplicaPoint::model()->findAllByAttributes(['dialog_id' => $replica->id]);
            /** @var ReplicaPoint[] $points */
            foreach($points as $point) {
                LogHelper::setDialogPoint($replica->id, $simulation->id, $point);
            }
        }

        $message = LibSendMs::sendMs($simulation, 'MS20');
        $this->appendNewMessage($logs, $message);

        $message = LibSendMs::sendMs($simulation, 'MS49');
        $this->appendNewMessage($logs, $message);

        $mgr->processLogs($simulation, $logs);

        // Require this for calculation 331 - 333 behaviors
        SimulationService::saveEmailsAnalyze($simulation);

        // This calls fill assessment aggregated data
        SimulationService::saveAggregatedPoints($simulation->id);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        $points = $simulation->assessment_points;
        $calculations = $simulation->assessment_calculation;
        $aggregated = $simulation->assessment_aggregated;

        foreach ($points as $row) {
            $details[$row->point->getTypeScaleSlug()][$row->point->code][] = $row->point->scale *$row->value;
        }

        foreach ($scaleTypes as $i => $scaleType) {
            $details[$scaleType] = isset($details[$scaleType]) ? array_map(function($item) {
                return array_sum($item) / count($item);
            }, $details[$scaleType]) : [];

            $details[$scaleType] = array_merge($details[$scaleType], array_map(function($item) use ($i) {
                return $item->point->type_scale == $i ? $item->value : 0;
            }, $calculations));

            $details[$scaleType] = array_sum($details[$scaleType]);

            $aggregatedCalculated[$scaleType] = array_sum(array_map(function($item) use ($i) {
                return $item->point->type_scale == $i ? $item->value : 0;
            }, $aggregated));

            $delta[$scaleType] = abs(round($details[$scaleType], 2) - round($aggregatedCalculated[$scaleType], 2));
        }

        var_dump($delta); die;

        $this->assertEquals(10, array_sum($delta)); #personal, no matter
    }

    public function testStressRules()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $eventsManager = new EventsManager();
        $dialog = new DialogService();

        $eventsManager->processLogs($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1]
        ]);

        // Action for rule id 2
        $replica = $simulation->game_type->getReplica(['excel_id' => 11]);
        $dialog->getDialog($simulation->id, $replica->id, '11:01');
        // end rule 2

        // Action for rule id 1
        $replica = $simulation->game_type->getReplica(['excel_id' => 522]);
        $dialog->getDialog($simulation->id, $replica->id, '12:02');
        // end rule 1

        // Action for rule id 13
        FlagsService::setFlag($simulation, 'F38_3', 1);
        $theme = $simulation->game_type->getCommunicationTheme(['phone_dialog_number' => 'T7.4']);
        PhoneService::call($simulation, $theme->id, 3, '12:03');
        // end rule 13

        // Actions for rule id 15
        LibSendMs::sendMsByCode($simulation, 'MS20', 40000, 1, 1, 1);
        // End rule 15

        $eventsManager->processLogs($simulation, [
            [1, 1, 'deactivated', 41000, 'window_uid' => 1]
        ]);

        SimulationService::simulationStop($simulation);

        $executedRules = StressPoint::model()->bySimId($simulation->id)->findAll();
        $list = array_map(function($rule) {
            return $rule->stressRule->code;
        }, $executedRules);
        sort($list);

        $this->assertEquals([1, 2, 13, 15], $list);
    }

    /**
     * Проверяет как система справляется с багом "в конце симуляции последний лог открывающий"
     */
    public function testSimulation_SimStopWithOpenLog()
    {
        $profile = YumProfile::model()->findByAttributes(['email' => 'asd@skiliks.com']);
        $user = $profile->user;
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $vacancy = Vacancy::model()->find();
        $positionLevel = PositionLevel::model()->find();
        $professionalOccupation = ProfessionalOccupation::model()->find();
        $professionalSpecialization = ProfessionalSpecialization::model()->find();

        if (null === $vacancy) {
            $vacancy = new Vacancy();
            $vacancy->label = 'test';
            $vacancy->professional_occupation_id = $professionalOccupation->id;
            $vacancy->professional_specialization_id = $professionalSpecialization->id;
            $vacancy->position_level_slug = $positionLevel->slug;
            $vacancy->save();
        }

        Invite::model()->deleteAllByAttributes(['email' => 'test@skiliks.com']);

        $invite = new Invite();
        $invite->firstname = 'test';
        $invite->lastname = 'test';
        $invite->email = 'test@skiliks.com';
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->receiver_id = $user->id;
        $invite->ownerUser = $user;
        $invite->owner_id = $user->id;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $invite->scenario_id = $scenario->id;
        $invite->vacancy_id = $vacancy->id;
        $invite->save();

        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL, Simulation::MODE_DEVELOPER_LABEL);
        $simulation->invite = $invite;
        $simulation->save();

        $logs = [];
        $logs[0][0]	= 1;
        $logs[0][1]	= 1;
        $logs[0][2]	= 'activated';
        $logs[0][3]	= 65115;
        $logs[0]['window_uid'] = 24;

        SimulationService::simulationStop($simulation, $logs);

        $this->assertEquals(true, true, 'SimStopWithOpenLog handled well!');
    }

    /**
     * All 100% - all points collected
     */
    public function testPerformanceAggregation_case_1()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        foreach ($simulation->game_type->getPreformanceRules([]) as $rule) {
            //if (0.5 < rand(0,1)) {
                $point = new PerformancePoint();
                $point->sim_id = $simulation->id;
                $point->performance_rule_id = $rule->id;
                $point->save();
            //}
        }

        SimulationService::calculatePerformanceRate($simulation);
        $evaluationService = new Evaluation($simulation);
        $evaluationService->checkManagerialProductivity();

        $ad = json_decode($simulation->getAssessmentDetails(), true);

        $this->assertEquals(5, count($ad[AssessmentCategory::PRODUCTIVITY]));

        $this->assertTrue(isset($ad[AssessmentCategory::PRODUCTIVITY][0]));
        $this->assertTrue(isset($ad[AssessmentCategory::PRODUCTIVITY][1]));
        $this->assertTrue(isset($ad[AssessmentCategory::PRODUCTIVITY][2]));
        $this->assertTrue(isset($ad[AssessmentCategory::PRODUCTIVITY]['2_min']));
        $this->assertTrue(isset($ad[AssessmentCategory::PRODUCTIVITY]['total']));

        $this->assertEquals(100, $ad[AssessmentCategory::PRODUCTIVITY][0], '0');
        $this->assertEquals(100, $ad[AssessmentCategory::PRODUCTIVITY][1], '1');
        $this->assertEquals(100, $ad[AssessmentCategory::PRODUCTIVITY][2], '2');
        $this->assertEquals(100, $ad[AssessmentCategory::PRODUCTIVITY]['2_min'], '2_min');
        $this->assertEquals(100, $ad[AssessmentCategory::PRODUCTIVITY]['total'], 'total');
    }

    /**
     *
     */
    public function testPerformanceAggregation_case_2()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        foreach ($simulation->game_type->getPreformanceRules([]) as $rule) {
            if (in_array($rule->code, [8, 18, 22])) {
                $point = new PerformancePoint();
                $point->sim_id = $simulation->id;
                $point->performance_rule_id = $rule->id;
                $point->save();
            }
        }

        SimulationService::calculatePerformanceRate($simulation);
        $evaluationService = new Evaluation($simulation);
        $evaluationService->checkManagerialProductivity();

        $ad = $simulation->getAssessmentDetails();

        $ad = json_decode($simulation->getAssessmentDetails(), true);

        $this->assertEquals(3, count($ad[AssessmentCategory::PRODUCTIVITY]));

        $this->assertTrue(isset($ad[AssessmentCategory::PRODUCTIVITY][2]));
        $this->assertTrue(isset($ad[AssessmentCategory::PRODUCTIVITY]['2_min']));
        $this->assertTrue(isset($ad[AssessmentCategory::PRODUCTIVITY]['total']));

        $this->assertNotNull($ad[AssessmentCategory::PRODUCTIVITY]['total']);
    }

    /* SK-2608 */
    /*public function testInviteMark() {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $invite->owner_id = $user->id;
        $invite->receiver_id = $user->id;
        $invite->firstname = $user->profile->firstname;
        $invite->lastname = $user->profile->lastname;
        $invite->scenario_id = $fullScenario->id;
        $invite->status = Invite::STATUS_ACCEPTED;
        $invite->sent_time = time(); // @fix DB!
        $invite->save(true, [
            'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status'
        ]);

        $invite->email = $user->profile->email;
        $invite->scenario = $fullScenario;
        $invite->receiverUser = $user;
        $invite->save(false);

        $simulation_bad = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);
        $simulation_good = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);
        $this->setTime($simulation_good, 10, 01, false);
        $this->setTime($simulation_bad, 10, 01, false);
        EventsManager::getState($simulation_good, []);
        try {
            EventsManager::getState($simulation_bad, []);
        }

        catch (InviteException $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');

    }*/

    public function testEmptyInviteForDev()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->status = $invite::STATUS_ACCEPTED;
        $invite->ownerUser = $user;
        $invite->owner_id = $user->id;
        $invite->scenario = $scenario;
        $invite->scenario_id = $scenario->id;
        $invite->save(false);

        $developerSim = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $this->setTime($developerSim, 10, 01, false);

        $this->assertArrayHasKey('result', EventsManager::getState($developerSim, []));

        // ---

        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->status = $invite::STATUS_ACCEPTED;
        $invite->ownerUser = $user;
        $invite->owner_id = $user->id;
        $invite->scenario = $scenario;
        $invite->scenario_id = $scenario->id;
        $invite->save(false);

        $promoSim = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL, Simulation::MODE_DEVELOPER_LABEL);

        $this->setTime($promoSim, 10, 01, false);

        $this->assertArrayHasKey('result', EventsManager::getState($promoSim, []));
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

