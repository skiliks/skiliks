<?php
class OneTest extends SeleniumTestCase
{
    /**
     * @large
     */
    public function testMyTestCase()
    {

        $this->markTestIncomplete();

        # Login
        $session = $this->webdriver->session('firefox');
        $this->startSimulation($session);
        # ожидание одного из компонентов (чтобы убедиться что симуляция стартовала)
        $this->waitForElement($session, 'xpath', '//a[@id="icons_documents"]');
        #старт теста по параметрам заданным Антоном

        # $this->assertEquals("Сумма оценок: 0", $session->element("css selector",".result-total")->text());


        $this->runEvent($session, "ET1.1");

        //$session->verifyTextPresent("Событие было успешно добавлено");

        #$this->assertEquals("Сумма оценок: 0", $session->element("css selector",".result-total")->text());

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

