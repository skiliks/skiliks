<?php

class PasswordTest extends SeleniumTestCase
{

    /**
     * @large
     */
    public function testPasswordRecovery() {
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url . 'site.php');
        $element = $this->waitForElement($session, 'xpath', "//input[@value='Регистрация']");

        $element->click("");
        $login = 'andrey' . time() . '@kostenko.name';
        $this->waitForElement($session, 'id', 'email')->value(array('value' => str_split($login)));
        $session->element('id','pass1')->value(array('value' => str_split('test')));
        $session->element('id','pass2')->value(array('value' => str_split('test')));
        $session->element('xpath','//input[@class="btn"]')->click();
        $session->open($this->browser_url . 'site.php');
        $this->waitForElement($session, 'xpath', "//input[@value='Вход']");
        $session->element('xpath','//input[@value="Забыли пароль?"]')->click();
        $session->element('id','email')->value(array('value' => str_split($login)));
        $session->element('xpath','//input[@value="Восстановить пароль"]')->click();
        $this->waitForElement($session, 'xpath', '//div[@id="message"]//a[@class="btn"]')->click();
        $this->assertEquals(1,1);
    }

}
