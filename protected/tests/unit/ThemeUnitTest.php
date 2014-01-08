<?php

class ThemeUnitTest extends CDbTestCase
{
    use UnitTestBaseTrait;

    /**
     * Проверяет что после отправки MS тема этого письма недоступна для написания нового
     */
    public function testMSNotUse()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // MS110
        $theme = $simulation->game_type->getTheme(['text' => 'Изменение бюджета производства вне регламента']);
        $boss  = $simulation->game_type->getCharacter(['fio' => 'Босс В.С.']);
        FlagsService::setFlag($simulation, 'F33', 1);

        $themes = MailBoxService::getThemes($simulation, $boss->id, null, $theme->id);

        $this->assertTrue(in_array($theme->text, $themes));

        LibSendMs::sendMsByCode($simulation, 'MS110');

        // А после отправки, такой темы не должно быть
        $themes = MailBoxService::getThemes($simulation, $boss->id, null, $theme->id);

        $this->assertFalse(in_array($theme->text, $themes));
    }

    /**
     * Проверяет что нельзя 2 раза позвонить одному и тому же человеку
     */
    public function testPhoneUse()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $character = $simulation->game_type->getCharacter(['fio'=>'Денежная Р.Р.']);
        $theme = $simulation->game_type->getTheme(['text' => 'Перенос сроков сдачи сводного бюджета']);

        FlagsService::setFlag($simulation, 'F32', 1);

        $themes = PhoneService::getThemes($character->code, $simulation);
        $this->assertTrue($this->findPhoneThemeByName($themes, $theme->text));

        PhoneService::call($simulation, $theme->id, $character->code, "10:00");

        $themes = PhoneService::getThemes($character->code, $simulation);
        $this->assertFalse($this->findPhoneThemeByName($themes, $theme->text));
    }
}