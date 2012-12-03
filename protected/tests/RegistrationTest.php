<?php

class RegistrationTest extends SeleniumTestCase
{

    /**
     * @large
     */
    public function testRegistration() {
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url);
        $element = $this->waitForElement($session, 'xpath', "//input[@value='Регистрация']");

        $element->click("");
        $login = 'andrey' . time() . '@kostenko.name';
        $this->waitForElement($session, 'id', 'email')->value(array('value' => str_split($login)));
        $session->element('id','pass1')->value(array('value' => str_split('test')));
        $session->element('id','pass2')->value(array('value' => str_split('test')));
        $session->element('xpath','//input[@class="btn"]')->click();
        $session->open($this->browser_url);
        $this->waitForElement($session, 'xpath', "//input[@value='Вход']");
        $session->element('id','login')->value(array('value' => str_split($login)));
        $session->element('id','pass')->value(array('value' => str_split('test')));
        $session->element('xpath','//input[@value="Вход"]')->click();
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию promo']")->click();
        $this->assertNotNull($this->waitForElement($session, 'xpath', '//a[@id="icons_phone"]'));
        # Cookies work
        $session->refresh();
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию promo']")->click();
        $session->close();
    }

}
