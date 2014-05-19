<?php

class ThemeUnitTest extends CDbTestCase
{
    use UnitTestBaseTrait;

    /**
     * 1. Проверяет что после отправки MS тема этого письма недоступна для написания нового
     * 2. Проверяет тему при написании правильного MS27 тема FWD содержит "Fwd: ".
     *    Был баг что темы было без $mailPrefix.
     */
    public function testMSNotUse()
    {
        $this->standardSimulationStart();

//         1) {
        // MS110
        $theme = $this->simulation->game_type->getTheme(['text' => 'Изменение бюджета производства вне регламента']);
        $boss  = $this->simulation->game_type->getCharacter(['fio' => 'Босс В.С.']);
        FlagsService::setFlag($this->simulation, 'F33', 1);

        $themes = MailBoxService::getThemes($this->simulation, $boss->id, null, $theme->id);

        $this->assertTrue(in_array($theme->text, $themes));

        LibSendMs::sendMsByCode($this->simulation, 'MS110');

        // А после отправки, такой темы не должно быть
        $themes = MailBoxService::getThemes($this->simulation, $boss->id, null, $theme->id);

        $this->assertFalse(in_array($theme->text, $themes));
//         1) }

        // 2) {
        /** @var MailBox $m8 */
        $trutnev = $this->simulation->game_type->getCharacter(['fio' => 'Трутнев С.']);
        $m8 = MailBoxService::copyMessageFromTemplateByCode($this->simulation, 'M8'); // письмо "!проблема с сервером!"

        // запрашиваю темя для форварда M8 к Трутневу
        $themes = MailBoxService::getThemes($this->simulation, $trutnev->id, 'fwd', $m8->theme->id);

        $this->assertEquals(1, count($themes));
        $this->assertEquals('Fwd: !проблема с сервером!', reset($themes));
        // 2) }
    }

    /**
     * Проверяет что нельзя 2 раза позвонить одному и тому же человеку
     */
    public function testPhoneUse()
    {
        $user = $this->initTestUserAsd();
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