<?php

class FlagCommunicationThemeDependenceUnitTest extends PHPUnit_Framework_TestCase {

    use UnitTestBaseTrait;

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
        $user = $this->initTestUserAsd();
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
        $user = $this->initTestUserAsd();
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
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Крутько М.']);

        $themes = MailBoxService::getThemes($simulation, $character->id, null, null);

        $this->assertFalse($this->findMailThemeByName($themes, 'Сводный бюджет: файл'));
    }

    public function testMailBoxServiceGetThemesNotBlocking() {

        /** @var $user YumUser */
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Крутько М.']);
        FlagsService::setFlag($simulation, 'F32', 1);
        $themes = MailBoxService::getThemes($simulation, $character->id, null, null);

        $this->assertTrue($this->findMailThemeByName($themes, 'Сводный бюджет: файл'));
    }

    public function testMailBoxReFwdNotBlocking() {
        /** @var $user YumUser */
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        $flags = $simulation->game_type->getFlagOutboxMailThemeDependencies([]);
        foreach($flags as $flag) {
            if(null !== $flag->outboxMailTheme->mail_prefix) {
                $this->assertFalse($flag->outboxMailTheme->isBlockedByFlags($simulation));
            }
        }
    }

    public function testMailBoxFantasticNotBlocking() {
        /** @var $user YumUser */
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        $replicas = $simulation->game_type->getReplicas(['fantastic_result' => 1]);
        $fantastic = ['MS21','MS22','MS23','MS29'];
        $ms = [];
        //FlagsService::setFlag($simulation, 'F32', 1);
        foreach($replicas as $replica) {
            /* @var $replica Replica */
            if(substr($replica->next_event_code, 0, 2) === "MS" && false === in_array($replica->next_event_code, $ms) && false === in_array($replica->next_event_code, $fantastic)){
                $ms[] = $replica->next_event_code;
                $outboxMailTheme = $simulation->game_type->getOutboxMailTheme(['mail_code'=>$replica->next_event_code]);
                $this->assertNotNull($outboxMailTheme);
                $this->assertFalse($outboxMailTheme->isBlockedByFlags($simulation), $replica->next_event_code);
            }
        }

    }

    public function testMailGetThemesNotMSBlocking() {

        /** @var $user YumUser */
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Трутнев С.']);

        $mail_theme = $simulation->game_type->getOutboxMailTheme(['character_to_id'=>$character->id, 'mail_code'=>'MS106']);

        $themes = MailBoxService::getThemes($simulation, $character->id, null, null);

        $this->assertFalse($this->findMailThemeByName($themes, $mail_theme->theme->text));

        FlagsService::setFlag($simulation, 'F38_3', 1);

        $themes = MailBoxService::getThemes($simulation, $character->id, null, null);

        $this->assertTrue($this->findMailThemeByName($themes, $mail_theme->theme->text));
    }



}
