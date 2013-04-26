<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 4/26/13
 * Time: 11:40 AM
 * To change this template use File | Settings | File Templates.
 */
class Assessment_Goals_Areas_Overals extends CDbTestCase
{
    use UnitLoggingTrait;

    public function testAssessment_Goals_Areas_Overals_case1()
    {
        /*$user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        SimulationService::simulationStop($simulation);

        $this->addAssessmentAggregated($simulation, '214d0');
        $this->addAssessmentAggregated($simulation, '214d1');
        $this->addAssessmentAggregated($simulation, '214d2');
        $this->addAssessmentAggregated($simulation, '214d3');

        SimulationLearningArea::model()->findByAttributes(['sim_id'=>$simulation->id]);*/
    }

    // -----------------------------------------------------

    private function addAssessmentAggregated(Simulation $simulation, $code, $k = 1 )
    {
        if (is_string($code)) {
            $behaviour = $simulation->game_type->getHeroBehavour(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $behaviour = $code;
        }

        if (null == $behaviour) {
            return false;
        }

        $item = new AssessmentAggregated();
        $item->sim_id = $simulation->id;
        $item->point_id = $behaviour->id;
        $item->value = $k * $behaviour->scale;
        $item->save();
    }
}