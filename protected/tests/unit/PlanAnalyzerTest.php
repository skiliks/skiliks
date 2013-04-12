<?php
/**
 * Оценка План - 214a
 */

class PlanAnalyzerTest extends PHPUnit_Framework_TestCase {

    protected function addToPlan(Simulation $simulation, $code, $time, $day){
        $task = $simulation->game_type->getTask(['code'=>$code]);
        DayPlanService::addToPlan($simulation, $task->id, $time, $day);
    }
    /*
     * 214а1 "Составляет план на сегодня до 11 утра.
     * Заполнил задачами все слоты на сегодня и сохранил время на незапланированные дела"
     * 0% <= X <= 50% OR X > 100% -> 0% веса
     */
    public function test_check_214a1_0(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a1();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0.00', $point->value);

    }

    /*
     * 214а1 "Составляет план на сегодня до 11 утра.
     * Заполнил задачами все слоты на сегодня и сохранил время на незапланированные дела"
     * 50% < X <= 60% OR 90% < X <= 100% -> 33,3% веса
     */
    public function test_check_214a1_33_3(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        //P17 30 min 16:00
        $this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY); //90 min
        $this->addToPlan($simulation, 'P02', '11:15', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P03', '11:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P04', '12:15', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P05', '12:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P08', '15:45', DayPlanLog::TODAY); //180 min
        $this->addToPlan($simulation, 'P07', '16:30', DayPlanLog::TODAY); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a1();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('1.00', $point->value);

    }

    /*
     * 214а1 "Составляет план на сегодня до 11 утра.
     * Заполнил задачами все слоты на сегодня и
     * сохранил время на незапланированные дела"
     * 60% < X <= 70% OR 80% < X <= 90% -> 66,7% веса
     */
    public function test_check_214a1_66_7(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY); //90 min

        $this->addToPlan($simulation, 'P04', '12:15', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P05', '12:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P08', '15:45', DayPlanLog::TODAY); //180 min
        $this->addToPlan($simulation, 'P07', '16:30', DayPlanLog::TODAY); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a1();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('2.00', $point->value);

    }

    /*
     * 214а1 "Составляет план на сегодня до 11 утра.
     * Заполнил задачами все слоты на сегодня и сохранил
     * время на незапланированные дела"
     * 70% < X <= 80%  -> 100,0% веса
     */
    public function test_check_214a1_100(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY); //90 min

        $this->addToPlan($simulation, 'P05', '12:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P08', '15:45', DayPlanLog::TODAY); //180 min
        $this->addToPlan($simulation, 'P07', '16:30', DayPlanLog::TODAY); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a1();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('3.00', $point->value);

    }

    /*
     * 214а3 "Составляет полный план на все последующие
     * дни в этой же сессии по планированию - с утра (все задачи из
     * туду листа перенёс в форму планирования, туду лист исчез),
     * сделал это в начале дня (до всех остальных задач)"
     * по состоянию на 11:00 осталась хотя бы одна задача в списке Сделать = 0% веса
     */
    public function test_check_214a3_0(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        DayPlanService::copyPlanToLog($simulation, '660');

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a3();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a3']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0.00', $point->value);

    }

    /*
     * 214а3 "Составляет полный план на все последующие
     * дни в этой же сессии по планированию - с утра (все задачи из
     * туду листа перенёс в форму планирования, туду лист исчез),
     * сделал это в начале дня (до всех остальных задач)"
     * по состоянию на 11:00 нет ни одной задачи в списке Сделать
     * (все они размещены по Сегодня, Завтра, После отпуска) = 100% веса
     */
    public function test_check_214a3_1(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        DayPlanService::copyPlanToLog($simulation, '660');

        $logs = DayPlanLog::model()->findAllByAttributes(['sim_id'=>$simulation->id]);
        foreach ($logs as $log) {
            $log->day = DayPlanLog::TODAY;
            $log->update(false, ['day']);
        }

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a3();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a3']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('1.00', $point->value);

    }

    /*
     * 214а4 "Составляет полный план на ЗАВТРА в
     * конце рабочего дня (на конец дня все слоты на завтра заполнены)"
     * по состоянию на 18:00 игрового времени запланировано задач
     * на ЗАВТРА < 70% рабочего времени на ЗАВТРА = 0% веса
     */
    public function test_check_214a4_0() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P01', '9:00', DayPlanLog::TOMORROW); //90 min

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_18_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a4();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a4']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0.00', $point->value);

    }

    /*
     * 214а4 "Составляет полный план на ЗАВТРА в конце
     * рабочего дня (на конец дня все слоты на завтра заполнены)"
     * по состоянию на 18:00 игрового времени запланировано задач
     * на ЗАВТРА >= 70% рабочего времени на ЗАВТРА = 100% веса
     */
    public function test_check_214a4_100() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P01', '9:00', DayPlanLog::TOMORROW); //90 min
        $this->addToPlan($simulation, 'P02', '11:30', DayPlanLog::TOMORROW); //30 min
        $this->addToPlan($simulation, 'P03', '12:00', DayPlanLog::TOMORROW); //30 min
        $this->addToPlan($simulation, 'P04', '12:30', DayPlanLog::TOMORROW); //30 min
        $this->addToPlan($simulation, 'P05', '13:00', DayPlanLog::TOMORROW); //30 min
        $this->addToPlan($simulation, 'P07', '13:30', DayPlanLog::TOMORROW); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_18_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a4();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a4']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('1.25', $point->value);

    }

    /*
     * 214а5 "Разносит ВСЕ задачи из "сделать" в конце
     * рабочего дня (на конец дня сегодня не осталось задач в туду листе)"
     * по состоянию на 18:00 осталась хотя бы одна задача в списке Сделать = 0% веса
     */
    public function test_check_214a5_0(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_18_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a5();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a5']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0.00', $point->value);

    }

    /*
     * 214а5 "Разносит ВСЕ задачи из "сделать" в конце
     * рабочего дня (на конец дня сегодня не осталось задач в туду листе)"
     * по состоянию на 18:00 нет ни одной задачи в списке Сделать = 100% веса
     */
    public function test_check_214a5_1(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_18_00);

        $logs = DayPlanLog::model()->findAllByAttributes(['sim_id'=>$simulation->id]);
        foreach ($logs as $log) {
            $log->day = DayPlanLog::TODAY;
            $log->update(false, ['day']);
        }

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a5();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a5']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0.75', $point->value);
    }

    /*
     * 214a8 "Не планирует вообще. Ни один слот на сегодня и завтра не заполнен задачами"
     * По состоянию на 11.00 нет ни одной задачи (кроме жестких) на Сегодня ЛИБО Завтра. = 100% веса
     */
    public function test_check_214a8_1(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_11_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a8();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a8']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('-4.00', $point->value);

    }

    /*
     * 214a8 "Не планирует вообще. Ни один слот на сегодня и завтра не заполнен задачами"
     * По состоянию на 11.00 есть хотя бы одна задача (кроме жестких) на Сегодня ЛИБО Завтра. = 0% веса
     */
    public function test_check_214a8_0(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_11_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a8();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a8']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0.00', $point->value);
    }

    /**
     *
     */
    public function test_check_214b_case1()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P6',   '10:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P012', '10:45', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P3',   '11:45', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P015', '12:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P04',  '13:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P06',  '13:45', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P018', '15:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P017', '16:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P016', '17:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P011', '09:30', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P019', '10:15', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P07',  '11:15', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P05',  '12:45', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P08',  '13:45', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P09',  '16:45', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P03',  '17:15', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P01',  '18:00', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P02',  '',      DayPlanLog::AFTER_VACATION);
        $this->addToPlan($simulation, 'P12',  '',      DayPlanLog::AFTER_VACATION);
        $this->addToPlan($simulation, 'P010', '',      DayPlanLog::AFTER_VACATION);
        $this->addToPlan($simulation, 'P013', '',      DayPlanLog::AFTER_VACATION);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_11_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214b0_214b4('214b0', 0);
        $analyzer->check_214b0_214b4('214b1', 1);
        $analyzer->check_214b0_214b4('214b2', 2);
        $analyzer->check_214b0_214b4('214b3', 3);
        $analyzer->check_214b0_214b4('214b4', 4);

        $analyzer->check_214b5_6_8('214b5', 0);
        $analyzer->check_214b5_6_8('214b6', 1);
        $analyzer->check_214b5_6_8('214b8', 2);


        $analyzer->check_214b9();

        // -- 214b0 //



        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b0']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   =>  $simulation->id,
            'point_id' => $behaviour->id
        ]);

        $this->assertEquals('0.00', $point->value, '214b0');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214b0 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(1, $points, '214b0 : 0');
        unset($points);

        // --- 214b1 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b1']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals('0.00', $point->value, '214b1');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214b1 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(1, $points, '214b1 : 0');
        unset($points);

        // --- 214b2 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b2']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals('0.00', $point->value, '214b2');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214b2 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(4, $points, '214b2 : 0');
        unset($points);

        // --- 214b3 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b3']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(round($behaviour->scale * 0.57, 2), $point->value, '214b3');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(4, $points, '214b3 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(3, $points, '214b3 : 0');
        unset($points);

        // --- 214b4 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b4']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214b4');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(3, $points, '214b4 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b4 : 0');
        unset($points);

        // --- 214b5 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b5']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale*3, $point->value, '214b5');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(3, $points, '214b5 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b5 : 0');
        unset($points);

        // --- 214b6 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b6']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214b6');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(1, $points, '214b6 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b6 : 0');
        unset($points);

        // --- 214b8 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b8']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale*3, $point->value, '214b8');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(3, $points, '214b8 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b8 : 0');
        unset($points);

        // --- 214b9 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b9']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(0, $point->value, '214b9');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214b9 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(2, $points, '214b9 : 0');
        unset($points);
    }

    /**
     *
     */
    public function test_check_214b_case2()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P013', '10:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P011', '13:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P018', '14:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P3',   '15:30', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P017', '16:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P09',  '17:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P019', '18:00', DayPlanLog::TODAY);

        $this->addToPlan($simulation, 'P010', '9:45', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P01',  '11:00', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P06',  '12:45', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P04',  '15:00', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P03',  '16:15', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P02',  '17:30', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P012', '20:00', DayPlanLog::TOMORROW);

        $this->addToPlan($simulation, 'P016', '',      DayPlanLog::AFTER_VACATION);
        $this->addToPlan($simulation, 'P12',  '',      DayPlanLog::AFTER_VACATION);

        $this->addToPlan($simulation, 'P015', '',      DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P08',  '',      DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P07',  '',      DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P05',  '',      DayPlanLog::TODO);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_11_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214b0_214b4('214b0', 0);
        $analyzer->check_214b0_214b4('214b1', 1);
        $analyzer->check_214b0_214b4('214b2', 2);
        $analyzer->check_214b0_214b4('214b3', 3);
        $analyzer->check_214b0_214b4('214b4', 4);

        $analyzer->check_214b5_6_8('214b5', 0);
        $analyzer->check_214b5_6_8('214b6', 1);
        $analyzer->check_214b5_6_8('214b8', 2);

        $analyzer->check_214b9();

        // --- 214b0 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b0']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214b0');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(1, $points, '214b0 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b0 : 0');
        unset($points);

        // --- 214b1 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b1']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214b1');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(1, $points, '214b1 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b1 : 0');
        unset($points);

        // --- 214b2 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b2']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale * 0.75, $point->value, '214b2');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(3, $points, '214b2 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(1, $points, '214b2 : 0');
        unset($points);

        // --- 214b3 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b3']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(round($behaviour->scale * 0.75, 2), $point->value, '214b3');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(3, $points, '214b3 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(1, $points, '214b3 : 0');
        unset($points);

        // --- 214b4 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b4']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214b4');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(1, $points, '214b4 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b4 : 0');
        unset($points);

        // --- 214b5 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b5']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(0, $point->value, '214b5');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214b5 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b5 : 0');
        unset($points);

        // --- 214b6 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b6']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(0, $point->value, '214b6');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214b6 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b6 : 0');
        unset($points);

        // --- 214b8 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b8']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214b8');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(1, $points, '214b8 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b8 : 0');
        unset($points);

        // --- 214b9 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b9']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214b9');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(2, $points, '214b9 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b9 : 0');
        unset($points);

        // --- //
    }

    public function test_check_214b_case_my()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P6', '10:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P012', '10:45', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P3', '11:45', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P015', '12:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P04', '13:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P06', '13:45', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P018', '15:15', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P017', '16:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P016', '17:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P011', '09:30', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P019', '10:15', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P07', '11:15', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P05', '12:45', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P08', '13:45', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P09', '16:45', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P03', '17:15', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P01', '18:00', DayPlanLog::TOMORROW);
        $this->addToPlan($simulation, 'P02', '13:45', DayPlanLog::AFTER_VACATION);
        $this->addToPlan($simulation, 'P010', '13:45', DayPlanLog::AFTER_VACATION);
        $this->addToPlan($simulation, 'P12', '13:45', DayPlanLog::AFTER_VACATION);
        $this->addToPlan($simulation, 'P013', '9:00', DayPlanLog::TODAY);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_11_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214b0_214b4('214b0', 0);

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b0']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals($behaviour->scale, $point->value);
    }

    public function test_check_214b_case_nothing_was_planing()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $this->addToPlan($simulation, 'P6', '',   DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P012', '', DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P3', '',   DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P015', '', DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P04', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P06', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P018', '', DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P017', '16:00', DayPlanLog::TODAY);
        $this->addToPlan($simulation, 'P016', '', DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P011', '', DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P019', '', DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P07', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P05', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P08', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P09', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P03', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P01', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P02', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P010', '', DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P12', '',  DayPlanLog::TODO);
        $this->addToPlan($simulation, 'P013', '',  DayPlanLog::TODO);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_11_00);

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214b0_214b4('214b0', 0);

        // -- 214b0 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214b0']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(0, $point->value, '214b0');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214b0 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214b0 : 0');
        unset($points);
    }

    public function test_check_214d_case_1()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // log 1 {
        $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'window_id'   => $window->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'main screen';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 5;
        $log->start_time            = '09:45:02';
        $log->end_time              = '09:45:33';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 1 }

        // log 2 {
//        $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $activity = $simulation->game_type->getActivity(['code' => 'T1.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'plan';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 1;
        $log->start_time            = '09:45:34';
        $log->end_time              = '09:47:54';
        $log->duration              = 0;
        $log->save();
        // log 2 }

        // log 3 {
        $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'window_id'   => $window->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'main screen';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 5;
        $log->start_time            = '09:47:54';
        $log->end_time              = '09:51:57';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 3 }

        // log 4 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);

        $activity = $simulation->game_type->getActivity([
            'code' => 'T3.2.1',
        ]);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_DOCUMENTS;
        $log->leg_action            = 'D1';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '09:51:57';
        $log->end_time              = '10:01:27';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 4 }

        // log 5 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        // $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'ARS1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'dialog_id'   => $replica->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'RS1';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '10:01:28';
        $log->end_time              = '10:03:46';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 5 }

        // log 6 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T3.2.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'D1';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '10:09:50';
        $log->end_time              = '10:10:57';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 6 }

        // log 7 {
        $window = Window::model()->findByAttributes(['subtype' => 'phone main']);
        // $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'window_id'   => $window->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'phone main';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '10:10:57';
        $log->end_time              = '10:12:28';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 7 }

        // log 8 {
        $window = Window::model()->findByAttributes(['subtype' => 'phone main']);
        // $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        $replica = $simulation->game_type->getReplica(['code' => 'T3.1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T3.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'dialog_id'   => $replica->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_MANUAL_DIAL;
        $log->leg_action            = 'phone main';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '10:12:28';
        $log->end_time              = '10:14:43';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 8 }

        // log 9 {
        $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'window_id'   => $window->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'main screen';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 5;
        $log->start_time            = '10:14:43';
        $log->end_time              = '10:19:22';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 9 }

        // log 10 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D17']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T3.2.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'D17';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '10:19:22';
        $log->end_time              = '10:24:44';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 10 }

        // log 11 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T3.2.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'D1';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '10:24:44';
        $log->end_time              = '10:27:17';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 11 }

        // log 12 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D17']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T3.2.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'D17';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '10:27:17';
        $log->end_time              = '10:28:41';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 12 }

        // log 13 {
        $window = Window::model()->findByAttributes(['subtype' => 'phone main']);
        // $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'window_id'   => $window->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'phone main';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '10:28:41';
        $log->end_time              = '10:33:54';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 13 }

        // log 14 {
        $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'window_id'   => $window->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'main screen';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 5;
        $log->start_time            = '10:33:54';
        $log->end_time              = '11:33:58';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 14 }

        // log 15 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T3.2.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'D1';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '11:33:58';
        $log->end_time              = '11:36:21';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 15 }

        // log 16 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        // $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        $replica = $simulation->game_type->getReplica(['code' => 'RST2']);
        $activity = $simulation->game_type->getActivity(['code' => 'ARS2']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'dialog_id'   => $replica->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'RS1';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 3;
        $log->start_time            = '11:36:22';
        $log->end_time              = '11:38:24';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 16 }

        // log 17 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T3.2.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'D1';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '11:38:24';
        $log->end_time              = '11:39:47';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 17 }

        // log 18 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D2']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T2']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'D2';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 1;
        $log->start_time            = '11:39:48';
        $log->end_time              = '11:45:14';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 18 }

        // log 19 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        // $replica = $simulation->game_type->getReplica(['code' => 'RST1']);
        $activity = $simulation->game_type->getActivity(['code' => 'T3.2.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id' => $template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'D1';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '11:45:14';
        $log->end_time              = '11:49:07';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 19 }

        // log 20 {
        $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'window_id'   => $window->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'main screen';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 5;
        $log->start_time            = '11:49:07';
        $log->end_time              = '12:06:57';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 20 }

        // log 21 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        // $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        $replica = $simulation->game_type->getReplica(['code' => 'E2']);
        $activity = $simulation->game_type->getActivity(['code' => 'AE2a']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'dialog_id'   => $replica->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'E2';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 1;
        $log->start_time            = '12:06:57';
        $log->end_time              = '12:10:55';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 21 }

        // log 22 {
        $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_wait']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'window_id'   => $window->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action            = 'main screen';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 5;
        $log->start_time            = '12:10:55';
        $log->end_time              = '12:35:25';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 22 }

        // log 23 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $mail = $simulation->game_type->getMailTemplate(['code' => 'M76']);
        $activity = $simulation->game_type->getActivity(['code' => 'TM76']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $mail->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_INBOX;
        $log->leg_action            = 'M76';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = '2_min';
        $log->start_time            = '12:35:25';
        $log->end_time              = '12:36:15';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 23 }

        // log 24 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $mail = $simulation->game_type->getMailTemplate(['code' => 'MS20']);
        $activity = $simulation->game_type->getActivity(['code' => 'TM8']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $mail->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_OUTBOX;
        $log->leg_action            = 'M76';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 0;
        $log->start_time            = '12:36:15';
        $log->end_time              = '12:39:21';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 24 }

        // log 25 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $mail = $simulation->game_type->getMailTemplate(['code' => 'M76']);
        $activity = $simulation->game_type->getActivity(['code' => 'TM76']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $mail->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_INBOX;
        $log->leg_action            = 'M76';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = '2_min';
        $log->start_time            = '12:39:21';
        $log->end_time              = '12:36:36';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 25 }

        // log 26 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $mail = $simulation->game_type->getMailTemplate(['code' => 'MS20']);
        $activity = $simulation->game_type->getActivity(['code' => 'A_already_used']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $mail->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_OUTBOX;
        $log->leg_action            = 'M20';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = 5;
        $log->start_time            = '12:36:36';
        $log->end_time              = '12:42:40';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 26 }

        // log 27 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $mail = $simulation->game_type->getMailTemplate(['code' => 'M76']);
        $activity = $simulation->game_type->getActivity(['code' => 'TM76']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $mail->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_INBOX;
        $log->leg_action            = 'M76';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = '2_min';
        $log->start_time            = '12:42:40';
        $log->end_time              = '12:43:11';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 27 }

        // log 28 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $mail = $simulation->game_type->getMailTemplate(['code' => 'MS62']);
        $activity = $simulation->game_type->getActivity(['code' => 'TM73']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $mail->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_OUTBOX;
        $log->leg_action            = 'M62';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = '2_min';
        $log->start_time            = '12:43:11';
        $log->end_time              = '12:46:02';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 28 }

        // log 29 {
        // $window = Window::model()->findByAttributes(['subtype' => 'main screen']);
        $mail = $simulation->game_type->getMailTemplate(['code' => 'M76']);
        $activity = $simulation->game_type->getActivity(['code' => 'TM76']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $mail->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_INBOX;
        $log->leg_action            = 'M76';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = '2_min';
        $log->start_time            = '12:46:02';
        $log->end_time              = '13:01:21';
        $log->duration              = 0;
        $log->is_keep_last_category = null;
        $log->save();
        // log 29 }

        $simulation->refresh();

        $analyzer = new PlanAnalyzer($simulation);

        var_dump($analyzer->logActivityActionsAggregatedGroupByParent);
    }
}
