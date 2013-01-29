<?php
class F4Flag2 extends SeleniumTestCase
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

        #добавление события
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E1.3")));
        $this->waitForElement($session, "id", "addTriggerDelay")->value(array("value" => str_split("1")));
        #клик на "создать событие"
        $session->element("xpath", "//input[@value='Создать']")->click();
        #ждем исчезновения черной полосы загрузки и в появившемся окне тригера кликаем ОК
        sleep(3);
        $session->element('css selector','.alert a.btn')->click();     
        sleep(5);
        
        #4 ответа на диалог с Трутневым
       
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Сергей, привет! Ты не мог бы мне помочь? У меня тут полный аврал. Крутько занята, только на тебя надежда. Будем делать бюджет, сводный.\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- В чем именно ты не уверен?\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Я знаю, что ты справишься. Я тебе помогу, отвечу на все вопросы.\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Однако тебе все-таки придется выполнить это задание. Пересылаю тебе файл. Смотри внимательно и не тяни с вопросами. На все про все у тебя два часа.\"]")->click();

        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        
        
        
                    }
}

?>