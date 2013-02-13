<?php

class LoginTest extends SeleniumTestCase
{

    /**
     * @large
     */
    public function testMyTestCase()
    {
        $this->markTestIncomplete();

        $session = $this->webdriver->session('firefox');
        # после вызова open нужно сделать waitForElement, чтобы дождаться загрузки страницы
        $session->open($this->browser_url . 'site/');
        # вот таким извращенным способом вводится текст
        $this->waitForElement($session, "id", "login")->value(
            array("value" => str_split($this->email))
        );
        $this->waitForElement($session, "id", "pass")->value(array("value" => str_split("111")));
        $session->element("css selector", "input.btn-primary")->click();
        sleep(5);
        $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
        sleep(5);
        $session->element('xpath', '//a[@id="icons_todo"]')->click();

        //$session->element("xpath", "id=icons_todo)")->click();
        sleep(5);
        //$this->click("id=icons_todo");
        //$this->click("css=button");
        //$this->click("id=icons_email");
        //$this->click("id=mailEmulatorContentDiv");
        //$this->click("css=button.btn-cl");
        //$this->click("//input[@value='SIM стоп']");
        //$this->click("//input[@value='Выход']");
    }
}

