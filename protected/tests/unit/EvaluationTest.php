<?php

class EvaluationTest extends PHPUnit_Framework_TestCase {

    protected function addToPlan($simulation, $code, $time, $day){
        $task = Task::model()->findByAttributes(['code'=>$code]);
        DayPlanService::addToPlan($simulation, $task->id, $time, $day);
    }

    public function testBadEvaluation(){
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        /*$this->addToPlan($simulation, 'P01', '9:45', DayPlanLog::TODAY); //90 min

        $this->addToPlan($simulation, 'P04', '12:15', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P05', '12:45', DayPlanLog::TODAY); //30 min
        $this->addToPlan($simulation, 'P08', '15:45', DayPlanLog::TODAY); //180 min
        $this->addToPlan($simulation, 'P07', '16:30', DayPlanLog::TODAY);

        SimulationService::simulationStop($simulation);*/
        $asses = new AssessmentAggregated();
        $asses->point_id = 1;
        $asses->sim_id = $simulation->id;
        $asses->value = 34.50;
        $asses->save();

        $evaluation = new Evaluation($simulation->id);
        $evaluation->checkManagerialSkills();

        $sim = Simulation::model()->findByAttributes(['id'=>$simulation->id]);
        $this->assertEquals('34.50', $sim->managerial_skills);
        //$this->assertEquals('0.00', $sim->managerial_productivity);
        //$this->assertEquals('0.00', $sim->time_management_effectiveness);
        //$this->assertEquals('0.00', $sim->overall_manager_rating);

    }

}
