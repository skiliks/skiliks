<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 19.12.12
 * Time: 18:13
 * To change this template use File | Settings | File Templates.
 */
class DialogTest extends SeleniumTestCase
{
    /**
     * @large
     */
    public function testE1()
    {

        # Login
        $this->markTestIncomplete();
        $session = $this->webdriver->session('firefox');
        $this->startSimulation($session);
        $this->runEvent($session, "E1");
        # one letter
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Раиса Романовна, ну что вы так волнуетесь?! Я уже несколько дней только бюджетом и занимаюсь, до отпуска точно успею.\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Ну, с помощью Крутько я должен управиться в эти сроки.\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"-          Марина, есть срочная работа.\"]")->click();
        $this->waitForElement($session, 'xpath', '//p[text()="- А мне что делать? А ты не можешь пока отложить презентацию? Через пару часиков снова ею займешься."]')->click();
        $this->waitForElement($session, 'xpath', '//p[text()="- Да уж, ситуация... Значит так, быстренько зови сюда Трутнева, попробую его подключить."]')->click();
        $this->waitForElement($session, 'xpath', '//p[text()="-          Сергей, нужно сделать бюджет. Срочно. Тебе придется этим заняться."]')->click();
        $this->waitForElement($session, 'xpath', '//p[text()="- Я тебе сейчас перешлю файл, ты посмотри, и сразу приходи, если будут вопросы, разберемся."]')->click();
        sleep(40);
        $this->waitForElement($session, 'css selector', '#icons_phone')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="phone.getContacts()"]')->click();
        //$this->waitForElement($session, 'xpath', '//a[@onclick="phone.getThemes(2)"]')->click();
        $simulation = $this->user->simulations[0];
        $dialogs = LogDialogs::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        foreach ($dialogs as $dialog) {
            print_r($dialog->attributes);
        };
    }



}
