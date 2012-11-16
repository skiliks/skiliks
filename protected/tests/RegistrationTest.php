<?php

class RegistrationTest extends SeleniumTestCase
{
    public function testRegistration() {
        $this->open('/');
        $this->waitForXpathCount("//input[@value='Регистрация']", 1);
        $this->click("//input[@value='Регистрация']");
        $login = 'andrey' . time() . '@kostenko.name';
        $this->type('id=email', $login);
        $this->type('id=pass1', 'test');
        $this->type('id=pass2', 'test');
        $this->click('css=input.btn');
        $this->open('/');
        $this->waitForXpathCount("//input[@value='Вход']", 1);
        $this->type('id=login', $login);
        $this->type('id=pass', 'test');
        $this->click("css=input.btn");
        $this->waitForXpathCount("//input[@value='Начать симуляцию promo']", 1);
        $this->click("//input[@value='Начать симуляцию promo']");
        $this->waitForXpathCount('//a[@id="icons_phone"]', 1);
    }

}
