<?php
/**
 * Оценка План - 214a
 */

class PlanAnalyzerTest extends PHPUnit_Framework_TestCase {

    protected function addToPlan($simulation, $code, $time, $day){
        $task = Task::model()->findByAttributes(['code'=>$code]);
        DayPlanService::addToPlan($simulation, $task->id, $time, $day);
    }
    /*
     * 214а1 "Составляет план на сегодня до 11 утра.
     * Заполнил задачами все слоты на сегодня и сохранил время на незапланированные дела"
     * 0% <= X <= 50% OR X > 100% -> 0% веса
     */
    public function test_check_214a1_0(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);
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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);
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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        // Bug with several calculations and cache fixes this bug
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b0']);
        $point = AssessmentCalculation::model()->findByAttributes([
            'sim_id'=>$simulation->id,
            'point_id'=>$behaviour->id
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

        // --- //

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b1']);
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

        // --- //

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b2']);
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

        // --- //

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b3']);
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

        // --- //

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b4']);
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

        // --- //

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b5']);
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

        // --- //

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b6']);
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

        // --- //

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b8']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b9']);
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

        // --- //
    }

    /**
     *
     */
    public function test_check_214b_case2()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b0']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b1']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b2']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b3']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b4']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b5']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b6']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b8']);
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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b9']);
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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b0']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals($behaviour->scale, $point->value);
    }

    public function test_check_214b_case_nothing_was_planing()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Scenario::TYPE_FULL);

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

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b0']);
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
}
