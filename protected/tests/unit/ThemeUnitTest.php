<?php

class ThemeUnitTest extends CDbTestCase
{
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

    public function testMSNotUse()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $theme = $simulation->game_type->getCommunicationTheme(['letter_number'=>'MS10']);
        FlagsService::setFlag($simulation, 'F45', 1);

        $themes = MailBoxService::getThemes($simulation, $theme->character_id);

        $this->assertTrue($this->findMailThemeByName($themes, $theme->text));

        LibSendMs::sendMsByCode($simulation, 'MS10');

        /* @var $theme CommunicationTheme */
        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$theme->id, 'sim_id'=>$simulation->id]);

        $this->assertNotNull($log_theme);

        $themes = MailBoxService::getThemes($simulation, $theme->character_id);

        $this->assertFalse($this->findMailThemeByName($themes, $theme->text));

        $mail = LibSendMs::sendNotMs($simulation);

        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$mail->subject_id, 'sim_id'=>$simulation->id]);

        $this->assertNull($log_theme);
    }

    public function testPhoneUse()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Денежная Р.Р.']);
        $theme = $simulation->game_type->getCommunicationTheme(['character_id'=>$character->id, 'phone_dialog_number'=>'E1.3.3.1', 'text'=>'Перенос сроков сдачи сводного бюджета']);

        FlagsService::setFlag($simulation, 'F32', 1);

        $themes = PhoneService::getThemes($character->code, $simulation);
        $this->assertTrue($this->findPhoneThemeByName($themes, $theme->text));

        PhoneService::call($simulation, $theme->id, $character->code, "10:00");

        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$theme->id, 'sim_id'=>$simulation->id]);
        $this->assertNotNull($log_theme);

        $themes = PhoneService::getThemes($character->code, $simulation);
        $this->assertFalse($this->findPhoneThemeByName($themes, $theme->text));

        $theme = $simulation->game_type->getCommunicationTheme(['character_id'=>$character->id, 'phone_dialog_number'=>'AUTO', 'text'=>'Служебная записка о сервере. Срочно!']);

        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$theme->id, 'sim_id'=>$simulation->id]);
        $this->assertNull($log_theme);
    }

}