<?php
class F4Flag3 extends SeleniumTestCase
{
    public function testMyTestCase()
    {
        # Login
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url . 'site.php');
        # раскрыть окно на весь экран
        $session->window()->maximize();
        # из-за черной полосы загрузки, пришлось добавить временное ожидание
        sleep(3);
        # вводится текст
        $this->waitForElement($session, "id", "login")->value(
            array("value" => str_split("kaaaaav@gmail.com"))
        );
        # Ждём появления елемента и кликаем на него
        $this->waitForElement($session, "id", "pass")->value(array("value" => str_split("111")));
        # Кликаем на него
        $session->element("xpath", "//input[@class='btn']")->click();
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию developer']");
        $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
        # ожидание одного из компонентов (чтобы убедиться что симуляция стартовала)
        $this->waitForElement($session, 'xpath', '//a[@id="icons_documents"]');


        #старт теста по параметрам заданным Антоном



        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        
        
                    }
}

?>