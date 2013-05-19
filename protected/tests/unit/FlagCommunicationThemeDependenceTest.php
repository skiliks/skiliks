<?php

class FlagCommunicationThemeDependenceTest extends PHPUnit_Framework_TestCase {

    public function testPhoneServiceGetThemesBlocking() {

        /** @var $user YumUser */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Трутнев С.']);

        $themes = PhoneService::getThemes($character->code, $simulation);

        $this->assertFalse($this->findPhoneThemeByName($themes, 'Сводный бюджет: контроль'));
    }

    public function testPhoneServiceGetThemesNotBlocking() {

        /** @var $user YumUser */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Трутнев С.']);

        FlagsService::setFlag($simulation, 'F4', 1);
        FlagsService::setFlag($simulation, 'F13', 0);

        $themes = PhoneService::getThemes($character->code, $simulation);

        $this->assertTrue($this->findPhoneThemeByName($themes, 'Сводный бюджет: контроль'));
    }

    public function testMailBoxServiceGetThemesBlocking() {

        /** @var $user YumUser */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Крутько М.']);

        $themes = MailBoxService::getThemes($simulation, $character->id);

        $this->assertFalse($this->findMailThemeByName($themes, 'Сводный бюджет: файл'));
    }

    public function testMailBoxServiceGetThemesNotBlocking() {

        /** @var $user YumUser */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Крутько М.']);
        FlagsService::setFlag($simulation, 'F32', 1);
        $themes = MailBoxService::getThemes($simulation, $character->id);

        $this->assertTrue($this->findMailThemeByName($themes, 'Сводный бюджет: файл'));
    }

    public function findPhoneThemeByName($themes, $name) {
        foreach($themes as $theme){
            if($theme['themeTitle'] === $name){
                return true;
            }
        }
        return false;
    }

    public function findMailThemeByName($themes, $name) {
        foreach($themes as $theme){
            if($theme === $name){
                return true;
            }
        }
        return false;
    }

}
