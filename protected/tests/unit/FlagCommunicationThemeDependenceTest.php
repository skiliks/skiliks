<?php

class FlagCommunicationThemeDependenceTest extends PHPUnit_Framework_TestCase {

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

    public function testMailBoxReFwdNotBlocking() {
        /** @var $user YumUser */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        $flags = $simulation->game_type->getFlagCommunicationThemeDependencies([]);
        foreach($flags as $flag){
            /* @var $flag FlagCommunicationThemeDependence */
            if(!empty($flag->communicationTheme->mail_prefix) && $flag->communicationTheme->mail === '1') {
                $this->assertFalse($flag->communicationTheme->isBlockedByFlags($simulation));
            }
        }
    }

    public function testMailBoxFantasticNotBlocking() {
        /** @var $user YumUser */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        $replicas = $simulation->game_type->getReplicas(['fantastic_result' => 1]);
        $ms = [];
        foreach($replicas as $replica){
            /* @var $replica Replica */
            if(substr($replica->next_event_code, 0, 2) === "MS" && false === in_array($replica->next_event_code, $ms)){
                $ms[] = $replica->next_event_code;
                /* @var $theme CommunicationTheme */
                $theme = $simulation->game_type->getCommunicationTheme(['letter_number'=>$replica->next_event_code]);
                $this->assertNotNull($theme);
                $this->assertFalse($theme->isBlockedByFlags($simulation));
            }
        }

    }


}
