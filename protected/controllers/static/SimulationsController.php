<?php

class SimulationsController extends SiteBaseController implements AccountPageControllerInterface
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
    public function actionIndexNew()
    {
        $user = Yii::app()->user;
        if (null === $user->id) {
            //Yii::app()->user->setFlash('error', 'Авторизируйтесь.');
            $this->redirect('/');
        }

        /**
         * @var YumUser $user
         */
        $user = $user->data();  //YumWebUser -> YumUser

        if (null === Yii::app()->user->data()->getAccount()) {
            $this->redirect('/registration/choose-account-type');
        }

        // check and add trial full version {
        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $tutorialScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_TUTORIAL]);

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
            $newInviteForFullSimulation->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
            $newInviteForFullSimulation->tutorial_scenario_id = $tutorialScenario->id;
            $newInviteForFullSimulation->is_display_simulation_results = 1;
            $newInviteForFullSimulation->save(true, [
                'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status', 'tutorial_scenario_id',
                'updated_at', 'is_display_simulation_results',
            ]);

            $newInviteForFullSimulation->email = strtolower(Yii::app()->user->data()->profile->email);
            $newInviteForFullSimulation->save(false);

            InviteService::logAboutInviteStatus($newInviteForFullSimulation, 'invite : created : system-demo (full 1)');
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
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedLiteSimulations)) {
            Invite::addFakeInvite(Yii::app()->user->data(), $liteScenario);
        }
        // check and add trial lite version }

        $this->layout = 'site_standard';
        $this->render('//new/simulations_corporate_new', []);
    }

    /**
     *
     */
    public function actionPersonal()
    {
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
            foreach ($notUsedLiteSimulations as $key => $notUsedFullSimulation) {
                if (0 < $i) {
                    $notUsedFullSimulation->delete();
                    unset($notUsedLiteSimulations[$key]);
                }
                $i++;
            }
        }
        // I remove more than 1 allowed to start lite sim }


        if (0 === count($notUsedLiteSimulations)) {
            $tutorialScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_TUTORIAL]);
            $newInviteForFullSimulation = new Invite();
            $newInviteForFullSimulation->owner_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->receiver_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->firstname = Yii::app()->user->data()->profile->firstname;
            $newInviteForFullSimulation->lastname = Yii::app()->user->data()->profile->lastname;
            $newInviteForFullSimulation->scenario_id = $liteScenario->id;
            $newInviteForFullSimulation->status = Invite::STATUS_ACCEPTED;
            $newInviteForFullSimulation->sent_time = time(); // @fix DB!
            $newInviteForFullSimulation->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
            $newInviteForFullSimulation->is_display_simulation_results = 1;
            $newInviteForFullSimulation->save(true, [
                'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status', 'is_display_simulation_results'
            ]);

            $newInviteForFullSimulation->email = strtolower(Yii::app()->user->data()->profile->email);
            $newInviteForFullSimulation->save(false);
            InviteService::logAboutInviteStatus($newInviteForFullSimulation, 'invite : created : system-demo (full 2)');
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
        $tutorialScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_TUTORIAL]);

        $notUsedFullSimulationInvites = Invite::model()->findAllByAttributes([
            'receiver_id' => Yii::app()->user->data()->id,
            'scenario_id' => $fullScenario->id,
            'email'       => Yii::app()->user->data()->profile->email,
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedFullSimulationInvites)) {
            $i = 0;
            foreach ($notUsedFullSimulationInvites as $key => $notUsedFullSimulation) {

                if (0 < $i) {
                    $notUsedFullSimulation->delete();
                    unset($notUsedFullSimulationInvites[$key]);
                }
                $i++;
            }
        }

        if (isset($notUsedFullSimulationInvites[0])) {
            $notUsedFullSimulationInvite = $notUsedFullSimulationInvites[0];
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedFullSimulationInvites)) {
            $newInviteForFullSimulation = new Invite();
            $newInviteForFullSimulation->owner_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->receiver_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->firstname = Yii::app()->user->data()->profile->firstname;
            $newInviteForFullSimulation->lastname = Yii::app()->user->data()->profile->lastname;
            $newInviteForFullSimulation->scenario_id = $fullScenario->id;
            $newInviteForFullSimulation->status = Invite::STATUS_ACCEPTED;
            $newInviteForFullSimulation->sent_time = time(); // @fix DB!
            $newInviteForFullSimulation->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
            $newInviteForFullSimulation->tutorial_scenario_id = $tutorialScenario->id;
            $newInviteForFullSimulation->is_display_simulation_results = 1;
            $newInviteForFullSimulation->save(true, [
                'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status', 'tutorial_scenario_id',
                'updated_at', 'is_display_simulation_results',
            ]);

            $newInviteForFullSimulation->email = strtolower(Yii::app()->user->data()->profile->email);
            $newInviteForFullSimulation->save(false);

            InviteService::logAboutInviteStatus($newInviteForFullSimulation, 'invite : created : system-demo (full 3)');

            $notUsedFullSimulationInvite = $newInviteForFullSimulation;
        }
        // check and add trial full version }

        // check and add trial lite version {
        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $notUsedLiteSimulationInvites = Invite::model()->findAllByAttributes([
            'receiver_id' => Yii::app()->user->data()->id,
            'scenario_id' => $liteScenario->id,
            'email'       => Yii::app()->user->data()->profile->email,
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedLiteSimulationInvites)) {
            $i = 0;
            foreach ($notUsedLiteSimulationInvites as $key => $notUsedLiteSimulation) {
                if (0 < $i) {
                    $notUsedLiteSimulation->delete();
                    unset($notUsedLiteSimulationInvites[$key]);
                }
                $i++;
            }
        }
        // I remove more than 1 allowed to start lite sim }

        if (isset($notUsedLiteSimulationInvites[0])) {
            $notUsedLiteSimulationInvite = $notUsedLiteSimulationInvites[0];
        }

        if (0 === count($notUsedLiteSimulationInvites)) {
            $notUsedLiteSimulationInvite = Invite::addFakeInvite(Yii::app()->user->data(), $liteScenario);
         }
        // check and add trial lite version }

        $this->render('simulations_corporate', [
            'notUsedFullSimulationInvite' => $notUsedFullSimulationInvite,
            'notUsedLiteSimulationInvite' => $notUsedLiteSimulationInvite
        ]);
    }

    /**
     *
     */
    public function actionDetails($id)
    {
        $simulation = Simulation::model()->findByPk($id);
        /* @var $user YumUser */
        $user = Yii::app()->user->data();
        if( false === $user->isAdmin() && null !== $simulation->invite){
            if ($user->id !== $simulation->invite->owner_id &&
                $user->id !== $simulation->invite->receiver_id) {
                //echo 'Вы не можете просматривать результаты чужих симуляций.';

                Yii::app()->end(); // кошерное die;
            }
        }

        if (false === $simulation->invite->isAllowedToSeeResults(Yii::app()->user->data())) {
            Yii::app()->end(); // кошерное die;
        }

        $this->layout = false;

        $details = $simulation->getAssessmentDetails();

        // update sim results popup info:
        $simulation->results_popup_partials_path = '//static/simulations/partials/';
        $simulation->save(false);

        $baseView = str_replace('partials/', 'simulation_details', $simulation->results_popup_partials_path);

        $this->render($baseView, [
            'simulation'     => $simulation,
            'details'        => $details,
            'user'           => $user
        ]);
    }
}