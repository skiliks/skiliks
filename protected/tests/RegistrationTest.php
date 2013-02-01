<?php

class RegistrationTest extends SeleniumTestCase
{

    /**
     * @large
     */
    public function testRegistration() {
        $this->markTestIncomplete();
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url . 'site/');
        $element = $this->waitForElement($session, 'xpath', "//input[@value='Регистрация']");

        $element->click("");
        $login = 'andrey' . time() . '@kostenko.name';
        $this->waitForElement($session, 'id', 'email')->value(array('value' => str_split($login)));
        $session->element('id','pass1')->value(array('value' => str_split('test')));
        $session->element('id','pass2')->value(array('value' => str_split('test')));
        $element = $this->waitForElement($session, 'xpath', "//input[@value='Регистрация']")->click();
        $this->waitForElement($session, "css selector", "input.btn-primary");
        $session->element('id','login')->value(array('value' => str_split($login)));
        $session->element('id','pass')->value(array('value' => str_split('test')));
        $session->element("css selector", "input.btn-primary")->click();
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию promo']")->click();
        $this->assertNotNull($this->waitForElement($session, 'xpath', '//a[@id="icons_phone"]'));
        # Cookies work
        $session->refresh();
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию promo']")->click();
        $session->close();
    }

}
