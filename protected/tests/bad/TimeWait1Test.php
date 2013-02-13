<?php
class TimeWait1Test extends SeleniumTestCase
{
    public function testMyTestCase()
    {
        $this->markTestIncomplete();
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
        
        #задаем время 11 28
        
        $this->waitForElement($session, "id", "newTimeH")->value(array("value" => str_split("11")));
        $this->waitForElement($session, "id", "newTimeM")->value(array("value" => str_split("28")));
        $session->element("xpath", "//input[@value='Задать']")->click();
        
        #ждём и кликаем на дверь
        sleep(25);
        $this->waitForElement($session, 'xpath', '//a[@id="icons_visit"]')->click();
        
        #не впустить
        sleep(2);
        $session->element("xpath", "//div[@id='dialogControllerMainDiv']/section/div[2]/div/a[2]/span")->click();
        #слип для темной полосы
        sleep(2);
        
        
        #чистим поля для ввода времени
        $session->element("id", "newTimeH")->clear();
        $session->element("id", "newTimeM")->clear();
        
        #задаем время 13 57
        
        $this->waitForElement($session, "id", "newTimeH")->value(array("value" => str_split("13")));
        $this->waitForElement($session, "id", "newTimeM")->value(array("value" => str_split("57")));
        $session->element("xpath", "//input[@value='Задать']")->click();
        

        
        #ждём и кликаем на дверь
        sleep(55);
        $this->waitForElement($session, 'xpath', '//a[@id="icons_visit"]')->click();        
        sleep(5);

        #впустить
        #$this->waitForElement($session, "id", "dialogControllerMainDiv")->click();
        #$session->element("xpath", "//div[@id='dialogControllerMainDiv']/section/div[2]/div/a[2]/span")->click();
        #$this->click("//div[@id='dialogControllerMainDiv']/section/div[2]/div/a[2]/span");
        $session->element('css selector','div.visitor-btn > a > span')->click(); 
        
        
        
        sleep(20);
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Ладно, заходите. Надеюсь, это не надолго?\"]")->click();
        
        
            }
}

?>