<?php
class F910Test extends SeleniumTestCase
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
        $session->element("css selector", "input.btn-primary")->click();
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
        $this->waitForElement($session, 'css selector', "input[value='Начать симуляцию developer']");
        $session->element("css selector", "input[value='Начать симуляцию developer']")->click();
        # ожидание одного из компонентов (чтобы убедиться что симуляция стартовала)
        $this->waitForElement($session, 'xpath', '//a[@id="icons_documents"]');
 
        #старт теста по параметрам заданным Антоном

        #добавление события
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E2.4")));
        $this->waitForElement($session, "id", "addTriggerDelay")->value(array("value" => str_split("1")));
        #клик на "создать событие"
        $session->element("xpath", "//input[@value='Создать']")->click();
        #ждем исчезновения черной полосы загрузки и в появившемся окне тригера кликаем ОК
        sleep(3);
        $session->element('css selector','.alert a.btn')->click();     
        sleep(5);
        
        #2 ответа на диалог с Крутько
        
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Марина, срочно пересылай мне презентацию для Генерального! Босс сам звонил и интересовался!\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"-          Отлично, одной проблемой меньше. Жду в 15.30\"]")->click();
        
        sleep(3);
        
        #обнуляем поле ввода названия события, вписываем новое, кликаем создать
        $session->element("id", "addTriggerSelect")->clear();
        sleep(1);
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E12.1")));
        $session->element("xpath", "//input[@value='Создать']")->click();
        #подтвеждение тригера
        sleep(3);
        $session->element('css selector','.alert a.btn')->click();     
        sleep(5);
        
        #2 ответа на диалог с секретарем ГД
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Может мой аналитик подойти вместо меня?\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- В понедельник, скажем в 10.00, будет моя сотрудница Марина Крутько.\"]")->click();
        
        #обнуляем поле ввода названия события, вписываем новое, кликаем создать
        $session->element("id", "addTriggerSelect")->clear();
        sleep(1);
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E12.5")));
        $session->element("xpath", "//input[@value='Создать']")->click();
        #подтвеждение тригера
        sleep(3);
        $session->element('css selector','.alert a.btn')->click();     
        sleep(5);
        
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Действительно, повезло! Уже бегу!\"]")->click();
        sleep(3);
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Кхе-кхе…\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Да, доволен\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Валерий Семенович, в прошлый раз презентацию делал аналитик, который у нас уже не работает. Ваша презентация была не единственным его промахом.\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Это наши корпоративные цвета.\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Мы вместе с сотрудниками. Они готовили – я проверял.\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Хорошего вам выступления, Валерий Семенович! В этом году краснеть не придется!\"]")->click();

        #ставим 12 00 чтобы пришло письмо с сервером
        $this->waitForElement($session, 'xpath', "//a[text()=\"12:00 \"]")->click();
               
        #ждем пока придут письма
        sleep(3);
        
        //$session->element("id", "icons_email")->click();
        
        
                    }
}

?>