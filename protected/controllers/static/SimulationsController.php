<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/21/13
 * Time: 11:33 PM
 * To change this template use File | Settings | File Templates.
 */

class SimulationsController extends AjaxController implements AccountPageControllerInterface
{
    /**
     * @return string
     */
    public function getBaseViewPath()
    {
        return '/static/simulations';
    }

    /**
     *
     */
    public function actionIndex()
    {
        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionPersonal()
    {
        // check and add trial lite version {
        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $notUsedFullSimulations = Invite::model()->findAllByAttributes([
            'receiver_id' => Yii::app()->user->data()->id,
            'scenario_id' => $fullScenario->id,
            'email'       => Yii::app()->user->data()->profile->email,
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedFullSimulations)) {
            $i = 0;
            foreach ($notUsedFullSimulations as $key => $notUsedFullSimulation) {
                if (0 < $i) {
                    $notUsedFullSimulation->delete();
                    unset($notUsedFullSimulations[$key]);
                }
                $i++;
            }
            $notUsedFullSimulations = [];
        }
        // I remove more than 1 allowed to start lite sim }


        if (0 === count($notUsedFullSimulations)) {
            $newInviteForFullSimulation = new Invite();
            $newInviteForFullSimulation->owner_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->receiver_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->firstname = Yii::app()->user->data()->profile->firstname;
            $newInviteForFullSimulation->lastname = Yii::app()->user->data()->profile->lastname;
            $newInviteForFullSimulation->scenario_id = $fullScenario->id;
            $newInviteForFullSimulation->status = Invite::STATUS_ACCEPTED;
            $newInviteForFullSimulation->sent_time = time(); // @fix DB!
            $newInviteForFullSimulation->save(true, [
                'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status'
            ]);

            $newInviteForFullSimulation->email = Yii::app()->user->data()->profile->email;
            $newInviteForFullSimulation->save(false);
        }
        // check and add trial lite version }

        $this->render('simulations_personal', []);
    }

    /**
     *
     */
    public function actionCorporate()
    {
        // check and add trial full version {
        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $notUsedFullSimulations = Invite::model()->findAllByAttributes([
            'receiver_id' => Yii::app()->user->data()->id,
            'scenario_id' => $fullScenario->id,
            'email'       => Yii::app()->user->data()->profile->email,
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedFullSimulations)) {
            $i = 0;
            foreach ($notUsedFullSimulations as $key => $notUsedFullSimulation) {
                if (0 < $i) {
                    $notUsedFullSimulation->delete();
                    unset($notUsedFullSimulations[$key]);
                }
                $i++;
            }
            $notUsedFullSimulations = [];
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedFullSimulations)) {
            $newInviteForFullSimulation = new Invite();
            $newInviteForFullSimulation->owner_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->receiver_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->firstname = Yii::app()->user->data()->profile->firstname;
            $newInviteForFullSimulation->lastname = Yii::app()->user->data()->profile->lastname;
            $newInviteForFullSimulation->scenario_id = $fullScenario->id;
            $newInviteForFullSimulation->status = Invite::STATUS_ACCEPTED;
            $newInviteForFullSimulation->sent_time = time(); // @fix DB!
            $newInviteForFullSimulation->save(true, [
                'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status'
            ]);

            $newInviteForFullSimulation->email = Yii::app()->user->data()->profile->email;
            $newInviteForFullSimulation->save(false);
        }
        // check and add trial full version }

        // check and add trial lite version {
        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $notUsedLiteSimulations = Invite::model()->findAllByAttributes([
            'receiver_id' => Yii::app()->user->data()->id,
            'scenario_id' => $liteScenario->id,
            'email'       => Yii::app()->user->data()->profile->email,
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedLiteSimulations)) {
            $i = 0;
            foreach ($notUsedLiteSimulations as $key => $notUsedLiteSimulation) {
                if (0 < $i) {
                    $notUsedLiteSimulation->delete();
                    unset($notUsedLiteSimulations[$key]);
                }
                $i++;
            }
            $notUsedLiteSimulations = [];
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedLiteSimulations)) {
            $newInviteForFullSimulation = new Invite();
            $newInviteForFullSimulation->owner_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->receiver_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->firstname = Yii::app()->user->data()->profile->firstname;
            $newInviteForFullSimulation->lastname = Yii::app()->user->data()->profile->lastname;
            $newInviteForFullSimulation->scenario_id = $liteScenario->id;
            $newInviteForFullSimulation->status = Invite::STATUS_ACCEPTED;
            $newInviteForFullSimulation->sent_time = time(); // @fix DB!
            $newInviteForFullSimulation->save(true, [
                'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status'
            ]);

            $newInviteForFullSimulation->email = Yii::app()->user->data()->profile->email;
            $newInviteForFullSimulation->save(false);
        }
        // check and add trial lite version }

        $this->render('simulations_corporate', []);
    }

    /**
     *
     */
    public function actionDetails($id)
    {
        $simulation = Simulation::model()->findByPk($id);

        $this->layout = false;

        $learning_areas = [];

        $learning_areas['resultOrientation'] = SimulationLearningArea::model()->findByAttributes(['sim_id'=>$simulation->id]);

        $this->render('simulation_details', [
            'simulation'     => $simulation,
            'learning_areas' => $learning_areas
        ]);
    }
}