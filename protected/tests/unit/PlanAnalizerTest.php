<?php
/**
 * Оценка План - 214a
 */

class PlanAnalizerTest extends PHPUnit_Framework_TestCase {

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a1();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        //P17 30 min 16:00
        $this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY); //90 min
        $this->addToPlan($simulation, 'P02', '11:15', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P03', '11:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P04', '12:15', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P05', '12:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P08', '15:45', DayPlanLog::TODAY); //180 min
        $this->addToPlan($simulation, 'P07', '16:30', DayPlanLog::TODAY); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a1();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY); //90 min

        $this->addToPlan($simulation, 'P04', '12:15', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P05', '12:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P08', '15:45', DayPlanLog::TODAY); //180 min
        $this->addToPlan($simulation, 'P07', '16:30', DayPlanLog::TODAY); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a1();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY); //90 min

        $this->addToPlan($simulation, 'P05', '12:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P08', '15:45', DayPlanLog::TODAY); //180 min
        $this->addToPlan($simulation, 'P07', '16:30', DayPlanLog::TODAY); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660');

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a1();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        DayPlanService::copyPlanToLog($simulation, '660');

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a3();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        DayPlanService::copyPlanToLog($simulation, '660');

        $task = Task::model()->findByAttributes(['code'=>'P017']);
        $log = DayPlanLog::model()->findByAttributes(['task_id'=>$task->id, 'sim_id'=>$simulation->id, 'snapshot_time'=>DayPlanLog::ON_11_00]);
        $log->todo_count = 0;
        $log->update();

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a3();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $this->addToPlan($simulation, 'P01', '9:00', DayPlanLog::TOMORROW); //90 min

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_18_00);

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a4();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $this->addToPlan($simulation, 'P01', '9:00', DayPlanLog::TOMORROW); //90 min
        $this->addToPlan($simulation, 'P02', '11:30', DayPlanLog::TOMORROW); //30 min
        $this->addToPlan($simulation, 'P03', '12:00', DayPlanLog::TOMORROW); //30 min
        $this->addToPlan($simulation, 'P04', '12:30', DayPlanLog::TOMORROW); //30 min
        $this->addToPlan($simulation, 'P05', '13:00', DayPlanLog::TOMORROW); //30 min
        $this->addToPlan($simulation, 'P07', '13:30', DayPlanLog::TOMORROW); // 90 min

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_18_00);

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a4();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_18_00);

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a5();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_18_00);

        $task = Task::model()->findByAttributes(['code'=>'P017']);
        $log = DayPlanLog::model()->findByAttributes(['task_id'=>$task->id, 'sim_id'=>$simulation->id, 'snapshot_time'=>DayPlanLog::ON_18_00]);
        $log->todo_count = 0;
        $log->update();

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a5();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_11_00);

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a8();

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY);

        DayPlanService::copyPlanToLog($simulation, '660', DayPlanLog::ON_11_00);

        $analizer = new PlanAnalizer($simulation->id);
        $analizer->check_214a8();

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a8']);
        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour->id]);
        $this->assertEquals('0.00', $point->value);

    }

}
