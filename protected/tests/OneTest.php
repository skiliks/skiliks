<?php
class OneTest extends SeleniumTestCase
{
    /**
     * @large
     */
    public function testMyTestCase()
    {

        # Login
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url . 'site.php');
        # раскрыть окно на весь экран
        $session->window()->maximize();
        # из-за черной полосы загрузки, пришлось добавить временное ожидание
        sleep(2);
        # вводится текст
        $this->waitForElement($session, "id", "login")->value(
            array("value" => str_split("kaaaaav@gmail.com"))
        );
        # Ждём появления елемента и кликаем на него
        $this->waitForElement($session, "id", "pass")->value(array("value" => str_split("111")));
        # Кликаем на него
        $session->element("css selector", "input.btn-primary")->click();
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию developer']");
        $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
        # ожидание одного из компонентов (чтобы убедиться что симуляция стартовала)
        $this->waitForElement($session, 'xpath', '//a[@id="icons_documents"]');
        #старт теста по параметрам заданным Антоном

        # $this->assertEquals("Сумма оценок: 0", $session->element("css selector",".result-total")->text());


        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("ET1.1")));
        $this->waitForElement($session, "id", "addTriggerDelay")->value(array("value" => str_split("1")));

        $session->element("xpath", "//input[@value='Создать']")->click();

        //$session->verifyTextPresent("Событие было успешно добавлено");

        #$this->assertEquals("Сумма оценок: 0", $session->element("css selector",".result-total")->text());

        sleep(3);
        $session->element('css selector', '.alert a.btn')->click();
        $this->waitForElement($session, 'css selector', 'li.phone.icon-active', 15);


        # Телефон

        $session->element('xpath', '//a[@id="icons_phone"]')->click();
        $this->waitForElement($session, 'css selector', '.phone-call-in-btn');
        $session->element("xpath", "//a[text()=\"ПРИНЯТЬ\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Раиса Романовна, ну что вы так волнуетесь?! Я уже несколько дней только бюджетом и занимаюсь, до отпуска точно успею.\"]");
        $session->element('css selector', 'li > p')->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Я пока не знаю, сколько времени мне потребуется. Но точно смогу проверить все цифры к вечеру.\"]");
        $session->element('css selector', 'li > p')->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Понял, открываю файл.\"]")->click();
        $this->waitForElement($session, 'css selector', ".documents.icon-active");

        $session->element("id", "addTriggerSelect")->clear();

        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("ET2.1")));


        $session->element("xpath", "//input[@value='Создать']")->click();


        sleep(3);
        $session->element('css selector', '.alert a.btn')->click();
        $this->waitForElement($session, 'css selector', 'li.phone.icon-active', 20);
        $session->element('xpath', '//a[@id="icons_phone"]')->click();
        sleep(5);

        $this->waitForElement($session, "xpath", "//a[text()=\"ОТКЛОНИТЬ\"]")->click();

        $this->waitForElement($session, 'css selector', 'li.phone.icon-active', 60);
        $session->element('xpath', '//a[@id="icons_phone"]')->click();
        $this->waitForElement($session, 'xpath', "//a[text()=\"ПРИНЯТЬ\"]")->click();

        $this->waitForElement($session, "xpath", "//p[text()=\"- Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать! А аналитика, который тогда напортачил, я уже уволил.\"]")->click();
        $this->waitForElement($session, "xpath", "//p[text()=\"-          Непременно, сейчас запланирую время на проверку\"]")->click();
        sleep(8);
        $this->assertEquals("Сумма оценок: 4.66666666666667", $session->element("css selector", ".result-total")->text());
        $this->assertEquals("Сумма оценок 6x: 4", $session->element("css selector", ".result-total-6x")->text());
        $this->assertEquals("Сумма оценок Negative: 0", $session->element("css selector", ".result-total-negative")->text());


    }
}

