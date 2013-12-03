<?php
trait UnitTestBaseTrait {

    /**
     * @var YumUser
     */
    public $user;

    /**
     * @var Invite
     */
    public $invite;

    /**
     * @var Simulation
     */
    public $simulation;

    /**
     * init asd@skiliks.com to $this->user
     */
    private function initTestUserAsd()
    {
        $profile = YumProfile::model()->findByAttributes(['email' => 'asd@skiliks.com']);
        $this->user = $profile->user;
    }

    /**
     * init standard invite and simulation
     * useful for 95% of tests
     */
    private function standardSimulationStart()
    {
        $this->initTestUserAsd();
        $this->invite = new Invite();
        $this->invite->scenario = new Scenario();
        $this->invite->receiverUser = $this->user;
        $this->invite->scenario->slug = Scenario::TYPE_FULL;
        $this->simulation = SimulationService::simulationStart($this->invite, Simulation::MODE_DEVELOPER_LABEL);
    }
}