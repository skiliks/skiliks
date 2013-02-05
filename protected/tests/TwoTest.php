<?php
class TwoTest extends SeleniumTestCase
{
    public function testMyTestCase()
    {
        $this->markTestIncomplete();
        # Login
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url . 'site/');
        # раскрыть окно на весь экран
        $session->window()->maximize();
        # из-за черной полосы загрузки, пришлось добавить временное ожидание
        sleep(3);
        # вводится текст
        $this->waitForElement($session, "id", "login")->value(
            array("value" => str_split($this->email))
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

        #добавление события
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E1")));
        $this->waitForElement($session, "id", "addTriggerDelay")->value(array("value" => str_split("1")));
        #клик на "создать событие"
        $session->element("xpath", "//input[@value='Создать']")->click();
        #ждем исчезновения черной полосы загрузки и в появившемся окне тригера кликаем ОК
        sleep(3);
        $session->element('css selector','.alert a.btn')->click();     
        sleep(5);
        
        #2 ответа на диалог с Денежной
       
        $this->waitForElement($session, 'xpath', "//p[text()=\" — Раиса Романовна, ну что вы так волнуетесь?! Я уже несколько дней только бюджетом и занимаюсь, до отпуска точно успею.\"]", 30)->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\" — Хорошо, за три часа управлюсь.\"]")->click();
        sleep(3);
        

        #обнуляем поле ввода названия события, вписываем новое, кликаем создать
        $session->element("id", "addTriggerSelect")->clear();
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E8.3")));
        $session->element("xpath", "//input[@value='Создать']")->click();
        #подтвеждение тригера, вейт почему то не работает
        sleep(3);
        $session->element('css selector','.alert a.btn')->click();     
        sleep(5);
        
        
        #3 ответа на диалог с начальником АйТи отдела за обедом
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Нет, прости, Мирон. Сегодня просто сумасшедший день. Собирался почту разбирать как только со срочными вопросами разберусь. А им конца и края нет.\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Семен, а у тебя наверняка в бюджете статейка есть на непредвиденные расходы. Это ведь форс-мажор?\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Хорошо, сейчас вернусь и напишу служебку. Спасибо за информацию!\"]")->click();
        
        sleep(10);


        #обнуляем поле ввода названия события, вписываем новое, кликаем создать
        $session->element("id", "addTriggerSelect")->clear();
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E12.1")));
        $session->element("xpath", "//input[@value='Создать']")->click();
        #подтвеждение тригера, вейт почему то не работает
        sleep(3);
        $session->element('css selector','.alert a.btn')->click();     
        sleep(5);
        
        
        #3 ответа на диалог
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Может мой аналитик подойти вместо меня?\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Хорошо, буду в 18.00\"]")->click();


        sleep(8);
        #проверка конечного результата
        $this->assertEquals("Сумма оценок: 4.5", $session->element("css selector", ".result-total")->text());
        $this->assertEquals("Сумма оценок 6x: 9", $session->element("css selector", ".result-total-6x")->text());
        $this->assertEquals("Сумма оценок Negative: 0", $session->element("css selector", ".result-total-negative")->text());

    }
}


