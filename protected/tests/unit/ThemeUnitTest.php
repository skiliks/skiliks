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
        $theme = CommunicationTheme::model()->findByAttributes(['letter_number'=>'MS10']);
        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$theme->id, 'sim_id'=>$simulation->id]);
        $this->assertNotNull($log_theme);
        $mail = LibSendMs::sendNotMs($simulation);
        $log_theme = LogCommunicationThemeUsage::model()->findByAttributes(['communication_theme_id'=>$mail->subject_id, 'sim_id'=>$simulation->id]);
        $this->assertNull($log_theme);
    }

}