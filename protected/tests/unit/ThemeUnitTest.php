<?php

class ThemeUnitTest extends CDbTestCase
{
    public function testMSNotUse()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        LibSendMs::sendMsByCode($simulation, 'MS10');
        $theme = $simulation->game_type->getCommunicationTheme(['letter_number'=>'MS10']);
        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$theme->id, 'sim_id'=>$simulation->id]);
        $this->assertNotNull($log_theme);
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
        PhoneService::call($simulation, $theme->id, $character->code, "10:00");

        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$theme->id, 'sim_id'=>$simulation->id]);
        $this->assertNotNull($log_theme);

        $theme = $simulation->game_type->getCommunicationTheme(['character_id'=>$character->id, 'phone_dialog_number'=>'AUTO', 'text'=>'Просьба']);

        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$theme->id, 'sim_id'=>$simulation->id]);
        $this->assertNull($log_theme);
    }

}