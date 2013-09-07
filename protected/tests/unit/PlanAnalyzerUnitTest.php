<?php
/**
 * Оценка План - 214a
 */

class PlanAnalyzerUnitTest extends PHPUnit_Framework_TestCase {

    protected function addToPlan(Simulation $simulation, $code, $day, $time = null) {
        $task = $simulation->game_type->getTask(['code'=>$code]);
        return DayPlanService::addTask($simulation, $task->id, $day, $time);
    }

    protected function addLog(PlanAnalyzer $pa, Simulation $simulation, $log, $is_ending = false) {
        $parent = $simulation->game_type->getActivityParentAvailability(['code'=>$log['parent']]);
        $var_214d = new LogActivityActionAgregated214d();
        $var_214d->sim_id = $simulation->id;
        $var_214d->leg_type = null;
        $var_214d->leg_action = null;
        $var_214d->activity_action_id = null;
        $var_214d->category = $parent->category;
        $var_214d->parent = $log['parent'];
        $var_214d->start_time = $log['start_time'];
        $var_214d->end_time = $log['end_time'];
        $var_214d->keep_last_category_initial = $pa->parents_keep_last_category[$log['parent']];
        $var_214d->keep_last_category_after = $pa->calcKeepLastCategoryAfter($log['start_time'], $log['end_time'], $var_214d->keep_last_category_initial);
        $var_214d->duration = '00:00:00';
        $var_214d->save();

        if($is_ending) {
            $sim_log = new SimulationCompletedParent();
            $sim_log->sim_id = $simulation->id;
            $sim_log->parent_code = $log['parent'];
            $sim_log->end_time = $log['end_time'];
            $sim_log->save();
        }
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
        $this->addToPlan($simulation, 'P01', DayPlan::DAY_1, '10:00'); //90 min
        $this->addToPlan($simulation, 'P02', DayPlan::DAY_1, '11:30'); //30 min
        $this->addToPlan($simulation, 'P03', DayPlan::DAY_1, '12:00'); //30 min
        $this->addToPlan($simulation, 'P04', DayPlan::DAY_1, '12:30'); //30 min
        $this->addToPlan($simulation, 'P08', DayPlan::DAY_1, '13:00'); //180 min
        $this->addToPlan($simulation, 'P07', DayPlan::DAY_1, '16:30'); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a1();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals(round($behaviour->scale * 33.3/100, 2), $point->value);

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


        $this->addToPlan($simulation, 'P01', DayPlan::DAY_1, '10:00'); //90 min
        $this->addToPlan($simulation, 'P02', DayPlan::DAY_1, '11:30'); //30 min
        $this->addToPlan($simulation, 'P03', DayPlan::DAY_1, '12:00'); //30 min
        $this->addToPlan($simulation, 'P04', DayPlan::DAY_1, '12:30'); //30 min
        $this->addToPlan($simulation, 'P05', DayPlan::DAY_1, '13:00'); //30 min
        $this->addToPlan($simulation, 'P08', DayPlan::DAY_1, '16:00'); //180 min
        $this->addToPlan($simulation, 'P07', DayPlan::DAY_1, '19:30'); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a1();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals(round($behaviour->scale * 66.7/100, 2), $point->value);

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


        $this->addToPlan($simulation, 'P01', DayPlan::DAY_1, '10:00'); //90 min
        $this->addToPlan($simulation, 'P02', DayPlan::DAY_1, '11:30'); //30 min
        $this->addToPlan($simulation, 'P03', DayPlan::DAY_1, '12:00'); //30 min
        $this->addToPlan($simulation, 'P04', DayPlan::DAY_1, '12:30'); //30 min
        $this->addToPlan($simulation, 'P05', DayPlan::DAY_1, '13:00'); //30 min
        $this->addToPlan($simulation, 'P10', DayPlan::DAY_1, '13:30'); // 60 min
        $this->addToPlan($simulation, 'P08', DayPlan::DAY_1, '16:00'); //180 min
        $this->addToPlan($simulation, 'P07', DayPlan::DAY_1, '19:30'); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214a1();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals($behaviour->scale, $point->value);

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

        $log->update(true, ['day']);

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


        $this->addToPlan($simulation, 'P01', DayPlan::DAY_2, '9:00'); //90 min

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


        $this->addToPlan($simulation, 'P01', DayPlan::DAY_2, '9:00'); //90 min
        $this->addToPlan($simulation, 'P02', DayPlan::DAY_2, '11:30'); //30 min
        $this->addToPlan($simulation, 'P03', DayPlan::DAY_2, '12:00'); //30 min
        $this->addToPlan($simulation, 'P04', DayPlan::DAY_2, '12:30'); //30 min
        $this->addToPlan($simulation, 'P05', DayPlan::DAY_2, '13:00'); //30 min
        $this->addToPlan($simulation, 'P07', DayPlan::DAY_2, '13:30'); // 90 min

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
        $log->update(true, ['day']);

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


        $this->addToPlan($simulation, 'P01', DayPlan::DAY_1, '10:00');

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


        $this->addToPlan($simulation, 'P6', DayPlan::DAY_1,   '10:15');
        $this->addToPlan($simulation, 'P012', DayPlan::DAY_1, '10:45');
        $this->addToPlan($simulation, 'P3', DayPlan::DAY_1,   '11:45');
        $this->addToPlan($simulation, 'P015', DayPlan::DAY_1, '12:15');
        $this->addToPlan($simulation, 'P04', DayPlan::DAY_1,  '13:15');
        $this->addToPlan($simulation, 'P06', DayPlan::DAY_1,  '13:45');
        $this->addToPlan($simulation, 'P018', DayPlan::DAY_1, '15:15');
        $this->addToPlan($simulation, 'P017', DayPlan::DAY_1, '16:00');
        $this->addToPlan($simulation, 'P016', DayPlan::DAY_1, '17:00');
        $this->addToPlan($simulation, 'P011', DayPlan::DAY_2, '09:30');
        $this->addToPlan($simulation, 'P019', DayPlan::DAY_2, '10:15');
        $this->addToPlan($simulation, 'P07', DayPlan::DAY_2,  '11:15');
        $this->addToPlan($simulation, 'P05', DayPlan::DAY_2,  '12:45');
        $this->addToPlan($simulation, 'P08', DayPlan::DAY_2,  '13:45');
        $this->addToPlan($simulation, 'P09', DayPlan::DAY_2,  '16:45');
        $this->addToPlan($simulation, 'P03', DayPlan::DAY_2,  '17:15');
        $this->addToPlan($simulation, 'P01', DayPlan::DAY_2,  '18:00');
        $this->addToPlan($simulation, 'P02', DayPlan::DAY_AFTER_VACATION);
        $this->addToPlan($simulation, 'P12', DayPlan::DAY_AFTER_VACATION);
        $this->addToPlan($simulation, 'P010', DayPlan::DAY_AFTER_VACATION);
        $this->addToPlan($simulation, 'P013', DayPlan::DAY_AFTER_VACATION);

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


        $this->addToPlan($simulation, 'P013', DayPlan::DAY_1, '10:00');
        $this->addToPlan($simulation, 'P011', DayPlan::DAY_1, '13:15');
        $this->addToPlan($simulation, 'P018', DayPlan::DAY_1, '14:00');
        $this->addToPlan($simulation, 'P3', DayPlan::DAY_1, '15:30');
        $this->addToPlan($simulation, 'P017', DayPlan::DAY_1, '16:00');
        $this->addToPlan($simulation, 'P09', DayPlan::DAY_1, '17:00');
        $this->addToPlan($simulation, 'P019', DayPlan::DAY_1, '18:00');

        $this->addToPlan($simulation, 'P010', DayPlan::DAY_2, '9:45');
        $this->addToPlan($simulation, 'P01', DayPlan::DAY_2,  '11:00');
        $this->addToPlan($simulation, 'P06', DayPlan::DAY_2,  '12:45');
        $this->addToPlan($simulation, 'P04', DayPlan::DAY_2,  '15:00');
        $this->addToPlan($simulation, 'P03', DayPlan::DAY_2,  '16:15');
        $this->addToPlan($simulation, 'P02', DayPlan::DAY_2,  '17:30');
        $this->addToPlan($simulation, 'P012', DayPlan::DAY_2, '20:00');

        $this->addToPlan($simulation, 'P016', DayPlan::DAY_AFTER_VACATION);
        $this->addToPlan($simulation, 'P12', DayPlan::DAY_AFTER_VACATION);

        $this->addToPlan($simulation, 'P015', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P08', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P07', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P05', DayPlan::DAY_TODO);

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

        $result[] = $this->addToPlan($simulation, 'P013', DayPlan::DAY_1, '10:00');
        $result[] = $this->addToPlan($simulation, 'P04', DayPlan::DAY_1, '13:00');
        $result[] = $this->addToPlan($simulation, 'P06', DayPlan::DAY_1, '13:30');
        $result[] = $this->addToPlan($simulation, 'P018', DayPlan::DAY_1, '15:00');
        $result[] = $this->addToPlan($simulation, 'P016', DayPlan::DAY_1, '17:00');
        $result[] = $this->addToPlan($simulation, 'P011', DayPlan::DAY_2, '09:30');
        $result[] = $this->addToPlan($simulation, 'P019', DayPlan::DAY_2, '10:15');
        $result[] = $this->addToPlan($simulation, 'P07', DayPlan::DAY_2, '11:15');
        $result[] = $this->addToPlan($simulation, 'P05', DayPlan::DAY_2, '12:45');
        $result[] = $this->addToPlan($simulation, 'P08', DayPlan::DAY_2, '13:45');
        $result[] = $this->addToPlan($simulation, 'P09', DayPlan::DAY_2, '16:45');
        $result[] = $this->addToPlan($simulation, 'P03', DayPlan::DAY_2, '17:15');
        $result[] = $this->addToPlan($simulation, 'P01', DayPlan::DAY_2, '18:00');
        $result[] = $this->addToPlan($simulation, 'P02', DayPlan::DAY_AFTER_VACATION);
        $result[] = $this->addToPlan($simulation, 'P010', DayPlan::DAY_AFTER_VACATION);
        $result[] = $this->addToPlan($simulation, 'P12', DayPlan::DAY_AFTER_VACATION);


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


        $this->addToPlan($simulation, 'P6', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P012', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P3', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P015', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P04', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P06', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P018', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P017', DayPlan::DAY_1, '16:00');
        $this->addToPlan($simulation, 'P016', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P011', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P019', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P07', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P05', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P08', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P09', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P03', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P01', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P02', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P010', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P12', DayPlan::DAY_TODO);
        $this->addToPlan($simulation, 'P013', DayPlan::DAY_TODO);

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
        $log->category              = 5;
        $log->start_time            = '10:01:28';
        $log->end_time              = '10:03:46';
        $log->duration              = 0;
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
        $log->end_time              = '12:39:36';
        $log->duration              = 0;
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
        $log->start_time            = '12:39:36';
        $log->end_time              = '12:42:40';
        $log->duration              = 0;
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
        $log->save();
        // log 29 }

        $simulation->refresh();

        $analyzer = new PlanAnalyzer($simulation);
        $analyzer->check_214d0_214d4('214d0', 0);
        $analyzer->check_214d0_214d4('214d1', 1);
        $analyzer->check_214d0_214d4('214d2', 2);
        $analyzer->check_214d0_214d4('214d3', 3);
        $analyzer->check_214d0_214d4('214d4', 4);

        $analyzer->check_214d5_6_8('214d5', 0, [4,5]);
        $analyzer->check_214d5_6_8('214d6', 1, [4,5]);
        $analyzer->check_214d5_6_8('214d8', 2, [4,5]);

        // -- 214b0 //
        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214d0']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   =>  $simulation->id,
            'point_id' => $behaviour->id
        ]);

        $this->assertEquals($behaviour->scale, $point->value, '214d0');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(2, $points, '214d0 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214d0 : 0');
        unset($points);

        // --- 214b1 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214d1']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214d1');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(1, $points, '214d1 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214d1 : 0');
        unset($points);

        // --- 214b2 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214d2']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals('0.00', $point->value, '214d2');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214d2 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214d2 : 0');
        unset($points);

        // --- 214b3 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214d3']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals($behaviour->scale, $point->value, '214d3');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(1, $points, '214d3 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214d3 : 0');
        unset($points);

        // --- 214b4 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214d4']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(0, $point->value, '214d4');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214d4 : 1');
        unset($points);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 0,
        ]);
        $this->assertEquals(0, $points, '214d4 : 0');
        unset($points);

        // --- 214d5 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214d5']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(0, $point->value, '214d5');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214d5 : 1');
        unset($points);

        // --- 214d6 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214d6']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(0, $point->value, '214d6');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214d6 : 1');
        unset($points);

        // --- 214d8 //

        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214d8']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
        ]);
        $this->assertEquals(0, $point->value, '214d8');
        unset($point);

        $points = AssessmentPlaningPoint::model()->countByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id,
            'value'             => 1,
        ]);
        $this->assertEquals(0, $points, '214d8 : 1');
        unset($points);
    }

    public function testParentEnding() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        EventsManager::startEvent($simulation, 'M8');
        $mail = EventsManager::getState($simulation, []);
        $log_mail = new LogMail();
        $log_mail->mail_id = $mail['events'][0]['id'];
        $log_mail->sim_id = $simulation->id;
        $log_mail->mail_task_id = null;
        $log_mail->full_coincidence = null;
        $log_mail->start_time = '11:00:20';
        $log_mail->end_time = '11:03:30';
        $log_mail->window = 13;
        $log_mail->window_uid = '34';
        $log_mail->save();
        //var_dump($mail['events'][0]['id']);

        // log 2 {
        $replica = $simulation->game_type->getReplica(['code' => 'T7.3']);
        $activity = $simulation->game_type->getActivity(['code' => 'T7.3']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'dialog_id'   => $replica->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'T7.3';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = $activity->category_id;
        $log->start_time            = '11:09:33';
        $log->end_time              = '12:10:55';
        $log->duration              = 0;
        $log->save();

        // log 4 {
        $replica = $simulation->game_type->getReplica(['code' => 'T7.5']);
        $activity = $simulation->game_type->getActivity(['code' => 'T7.5']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'dialog_id'   => $replica->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'T7.5';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = $activity->category_id;
        $log->start_time            = '12:12:55';
        $log->end_time              = '12:23:55';
        $log->duration              = 0;
        $log->save();

        // log 6 {
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
        $log->category              = $activity->category_id;
        $log->start_time            = '12:26:15';
        $log->end_time              = '12:27:21';
        $log->duration              = 0;
        $log->save();
        // log 6 }
        unset($log);
        $pn = new PlanAnalyzer($simulation);
        //var_dump($pn->logActivityActionsAggregatedGroupByParent);
        $log = $pn->logActivityActionsAggregatedGroupByParent[1];
        $this->assertEquals('14:10:55', $log['available']);
        $log = $pn->logActivityActionsAggregatedGroupByParent[2];
        $this->assertEquals('11:00:20', $log['available']);

    }

    public function testParentEndingHard() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // log 4 {
        $replica = $simulation->game_type->getReplica(['code' => 'T7.5']);
        $activity = $simulation->game_type->getActivity(['code' => 'T7.5']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'dialog_id'   => $replica->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'T7.5';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = $activity->category_id;
        $log->start_time            = '12:12:55';
        $log->end_time              = '12:23:55';
        $log->duration              = 0;
        $log->save();

        // log 6 {
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
        $log->category              = $activity->category_id;
        $log->start_time            = '15:26:15';
        $log->end_time              = '15:27:21';
        $log->duration              = 0;
        $log->save();
        // log 6 }
        unset($log);
        $pn = new PlanAnalyzer($simulation);
        //var_dump($pn->logActivityActionsAggregatedGroupByParent);
        $log = $pn->logActivityActionsAggregatedGroupByParent[0];
        $this->assertEquals('11:45:00', $log['available']);
        $log = $pn->logActivityActionsAggregatedGroupByParent[1];
        $this->assertEquals('14:35:00', $log['available']);
    }

    /**
     * Когда первое событие это начало парэнта
     */
    public function testParentEndingTM8_case_2() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $m8 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M8');

        // log 4 {
        $replica = $simulation->game_type->getReplica(['code' => 'T7.5']);
        $activity = $simulation->game_type->getActivity(['code' => 'T7.5']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'dialog_id'   => $replica->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action            = 'T7.5';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = $activity->category_id;
        $log->start_time            = '12:12:55';
        $log->end_time              = '12:23:55';
        $log->duration              = 0;
        $log->save();

        //--- mail M8 {

        $activity = $simulation->game_type->getActivity(['code' => 'TM8']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $m8->template->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_OUTBOX;
        $log->leg_action            = 'M8';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = $activity->category_id;
        $log->start_time            = '11:00:15';
        $log->end_time              = '11:02:21';
        $log->duration              = 0;
        $log->save();

        $logMail = new LogMail();
        $logMail->mail_id = $m8->id;
        $logMail->sim_id = $simulation->id;
        $logMail->window = null;
        $logMail->start_time = '11:00:14';
        $logMail->end_time = '11:02:20';
        $logMail->save();
        //--- mail M8 }

        // log 6 {
        $mail = $simulation->game_type->getMailTemplate(['code' => 'MS20']);
        $activity = $simulation->game_type->getActivity(['code' => 'TM8']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'mail_id'     => $mail->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_OUTBOX;
        $log->leg_action            = 'MS20';
        $log->activity_action_id    = (int)$activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = $activity->category_id;
        $log->start_time            = '10:00:15';
        $log->end_time              = '10:02:21';
        $log->duration              = 0;
        $log->save();

        $log = new LogActivityAction();
        $log->sim_id                = $simulation->id;
        $log->activity_action_id    = (int)$activityAction->id;
        $log->activityAction        = $activityAction;
        $log->start_time            = '10:00:15';
        $log->end_time              = '10:02:21';
        $log->save();
        // log 6 }
        unset($log);

        $pn = new PlanAnalyzer($simulation);

        $log = $pn->logActivityActionsAggregatedGroupByParent[0];

        $this->assertEquals('10:00:15', $log['available']);
        $log = $pn->logActivityActionsAggregatedGroupByParent[1];

        $this->assertEquals('11:45:00', $log['available']);

    }

    public function testLegD24(){
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $document = $simulation->game_type->getDocumentTemplate(['code' => 'D24']);
        $activity = $simulation->game_type->getActivity(['code' => 'T5.1']);
        $activityAction = $simulation->game_type->getActivityAction([
            'activity_id' => $activity->id,
            'document_id'     => $document->id,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id                = $simulation->id;
        $log->leg_type              = ActivityAction::LEG_TYPE_DOCUMENTS;
        $log->leg_action            = 'D24';
        $log->activity_action_id    = $activityAction->id;
        $log->activityAction        = $activityAction;
        $log->category              = $activity->category_id;
        $log->start_time            = '12:26:15';
        $log->end_time              = '12:27:21';
        $log->duration              = 0;
        $log->save();
        // log 6 }
        unset($log);
        $pn = new PlanAnalyzer($simulation);
        $this->assertEquals([], $pn->logActivityActionsAggregatedGroupByParent);
    }

    public function test214g0() {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214g0']);

        /* AE1 - category 0, ARS2 - keep last category, ARS3 - no priority */
        $pa = new PlanAnalyzer($simulation);
        $pa->check_214g('214g0', '0', []);

        $assessment = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0', $assessment->value);

        $this->addLog($pa, $simulation, [
            'parent' => 'AE1',
            'start_time'=>'11:01:48',
            'end_time'=>'11:22:26'
        ]); //no last
        $this->addLog($pa, $simulation, [
            'parent' => 'ARS3',
            'start_time'=>'11:22:26',
            'end_time'=>'11:31:37'
        ]); //no priority ====> fail

        $this->addLog($pa, $simulation, [
            'parent' => 'ARS2',
            'start_time'=>'11:56:00',
            'end_time'=>'11:57:26'
        ]); // keep last
        $this->addLog($pa, $simulation, [
            'parent' => 'AE1',
            'start_time'=>'11:57:26',
            'end_time'=>'12:11:56'
        ]); // no last
        $this->addLog($pa, $simulation, [
            'parent' => 'ARS2',
            'start_time'=>'12:11:56',
            'end_time'=>'12:41:00'
        ]); // keep last more 60 sec (real) ====> fail
        $this->addLog($pa, $simulation, [
            'parent' => 'AE1',
            'start_time'=>'12:41:00',
            'end_time'=>'12:45:56'
        ], true); // last
        $this->addLog($pa, $simulation, [
            'parent' => 'ARS3',
            'start_time'=>'12:45:56',
            'end_time'=>'14:31:37'
        ]); //

        $pa = new PlanAnalyzer($simulation);

        // hack - будем считать что это тест не чёрного, а белого ящика ;) {
        $pa->logAggregated214d = LogActivityActionAgregated214d::model()->findAllByAttributes([
            'sim_id' => $simulation->id,
        ]);
        // hack }

        $pa->check_214g('214g0', '0', []);

        $assessment = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $behaviour->id
        ]);
        $this->assertEquals(2*$behaviour->scale, $assessment->value);

    }

    public function test214g1() {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        $behaviour = $simulation->game_type->getHeroBehaviour(['code'=>'214g1']);

        $pa = new PlanAnalyzer($simulation);
        $pa->check_214g('214g1', '1', ['0']);

        $assessment = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0', $assessment->value);

        $this->addLog($pa, $simulation, [
            'parent' => 'T3a',
            'start_time'=>'09:57:39',
            'end_time'=>'10:03:54'
        ]);

        $this->addLog($pa, $simulation, [
            'parent' => 'ARS1',
            'start_time'=>'10:03:54',
            'end_time'=>'10:05:31'
        ]);

        $this->addLog($pa, $simulation, [
            'parent' => 'T3a',
            'start_time'=>'10:05:31',
            'end_time'=>'10:08:18'
        ]);

        $this->addLog($pa, $simulation, [
            'parent' => 'ARS7',
            'start_time'=>'10:10:45',
            'end_time'=>'10:19:56'
        ]);

        $this->addLog($pa, $simulation, [
            'parent' => 'T2',
            'start_time'=>'10:22:22',
            'end_time'=>'10:26:41'
        ]);
        $pa = new PlanAnalyzer($simulation);
        $pa->check_214g('214g1', '1', ['0']);
        $assessment = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0', $assessment->value);
    }

    public function test214g_for_sim_id_267()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $behaviour_214g0 = $simulation->game_type->getHeroBehaviour(['code'=>'214g0']);
        $behaviour_214g1 = $simulation->game_type->getHeroBehaviour(['code'=>'214g1']);
        $analyzer = new PlanAnalyzer($simulation);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'T3a',
            'start_time'=>'09:54:27',
            'end_time'=>'09:58:54'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'T2',
            'start_time'=>'09:58:54',
            'end_time'=>'10:01:18'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'T3a',
            'start_time'=>'10:01:18',
            'end_time'=>'10:30:57'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'AE10',
            'start_time'=>'10:30:57',
            'end_time'=>'10:31:44'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'T3a',
            'start_time'=>'10:31:44',
            'end_time'=>'10:33:12'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'AE10',
            'start_time'=>'10:33:12',
            'end_time'=>'10:33:46'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'TM8',
            'start_time'=>'11:15:01',
            'end_time'=>'11:20:02'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'T7a',
            'start_time'=>'11:23:46',
            'end_time'=>'11:50:20'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'AE2a',
            'start_time'=>'12:26:22',
            'end_time'=>'12:31:36'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'AE3',
            'start_time'=>'13:57:43',
            'end_time'=>'14:03:03'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'Category_5',
            'start_time'=>'14:16:22',
            'end_time'=>'14:18:07'
        ]);

        $this->addLog($analyzer, $simulation, [
            'parent' => 'T7b',
            'start_time'=>'14:19:27',
            'end_time'=>'14:20:42'
        ]);

        $simulation->refresh();

        $analyzer = new PlanAnalyzer($simulation);

        $analyzer->check_214g('214g0', '0', []);
        $analyzer->check_214g('214g1', '1', ['0']);

        $_214gLogs = LogActivityActionAgregated214d::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        $groupedLog = [];

        foreach($_214gLogs as $_214gLog) {
            $parentAvailability = $simulation->game_type->getActivityParentAvailability([
                'code' => $_214gLog->parent
            ]);

            $groupedLog[] = [
                'parent'      => $_214gLog->parent,
                'grandparent' => $_214gLog->parent,
                'category'    => $_214gLog->category,
                'start'       => $_214gLog->start_time,
                'end'         => $_214gLog->end_time,
                'available'   => $analyzer->calculateParentAvailability($parentAvailability, $groupedLog),
                'keepLastCategoryAfter60sec' => LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_YES ===
                    $analyzer->calcKeepLastCategoryAfter(
                        $_214gLog->start_time,
                        $_214gLog->end_time,
                        $parentAvailability->is_keep_last_category
                    )
            ];
        }

        $analyzer->logActivityActionsAggregatedGroupByParent = $groupedLog;

        $analyzer->check_214d0_214d4('214d1', 1);

        $behaviour = $simulation->game_type->getHeroBehaviour(['code' => '214d1']);

        $logs = AssessmentPlaningPoint::model()->findAllByAttributes([
            'sim_id'            => $simulation->id,
            'hero_behaviour_id' => $behaviour->id
        ]);

        $etalon = [
            'T2'   => 1,
            'T7a'  => 1,
            'AE2a' => 1,
            'T7b'  => 0
        ];

        // test 214d1
        foreach ($logs as $log) {
            $this->assertEquals($etalon[$log->activity_parent_code], $log->value);
        }

        $assessment214g0 = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour_214g0->id]);
        $this->assertEquals('0', $assessment214g0->value);

        $assessment214g1 = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour_214g1->id]);
        $this->assertEquals('0', $assessment214g1->value);
    }

    public function testDebug() {

        /*MyDocumentsService::restoreSCByLog('5144', 'D1');
        $simId = '5144';
        $email = 'fofanova.yp@rnd.eksmo.ru';
        SimulationService::CalculateTheEstimate($simId, $email);
        */
    }
}
