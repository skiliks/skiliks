<?php
class F4Flag1 extends SeleniumTestCase
{
    public function testMyTestCase()
    {
        $this->markTestIncomplete();
        # Login
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url);
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
        sleep(1);
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
        #$this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию developer']");
        $this->waitForElement($session, 'css selector', "input[value='Начать симуляцию developer']");
        $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
        # ожидание одного из компонентов (чтобы убедиться что симуляция стартовала)
        $this->waitForElement($session, 'xpath', '//a[@id="icons_documents"]');


        #старт теста по параметрам заданным Антоном

        #добавление события
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E1.3")));
        #$this->waitForElement($session, "id", "addTriggerDelay")->value(array("value" => str_split("1")));
        #клик на "создать событие"
        $session->element("xpath", "//input[@value='Создать']")->click();
        #ждем исчезновения черной полосы загрузки и в появившемся окне тригера кликаем ОК
        sleep(10);
        #$session->element('css selector','.alert a.btn')->click();     
        #sleep(5);
        
        #2 ответа на диалог с Трутневым

        $this->waitForElement($session, 'xpath', "//p[text()=\"— Сергей, привет! Ты не мог бы мне помочь? У меня тут полный аврал. Крутько занята, только на тебя надежда. Будем делать бюджет, сводный.\"]", 5)->click();
        sleep(2);
        $this->waitForElement($session, 'xpath', "//p[text()=\"— Я тебе сейчас перешлю файл, ты посмотри, и сразу приходи, если будут вопросы, разберемся.\"]")->click();
        sleep(20);
        
    
        $this->waitForElement($session, "id", "newTimeH")->value(array("value" => str_split("11")));
        $this->waitForElement($session, "id", "newTimeM")->value(array("value" => str_split("48")));
        $session->element("xpath", "//input[@value='Задать']")->click();
        sleep(2);
        
        
        $this->waitForElement($session, "id", "icons_phone", 20)->click;
        sleep(2);
        $this->waitForElement("xpath", "//input[@value='Трутнев С.']");
        
        $session->element("xpath", "//input[@value='Ответить']")->click();
         $this->waitForElement($session, 'xpath', "//p[text()=\" - Да, я тебе все переслал. Так в чем проблема, говори толком.\"]")->click();
        
        /* 
   $this->type("id=newTimeH", "11");
    $this->click("id=newTimeM");
    $this->type("id=newTimeM", "47");
    $this->click("//input[@value='Задать']");
    $this->click("id=icons_phone");
    $this->click("link=Не ПРИНЯТЬ");
    $this->click("id=icons_phone");
    $this->click("link=Ответить");
    $this->click("css=li > p");
    $this->click("//input[@value='SIM стоп']");



<a class="btn1" onclick="phone.getSelect('622',1)">Не ПРИНЯТЬ</a>


<a id="icons_phone" onclick="icons.showEvent('phone')"></a>
<a class="btn0" onclick="phone.getSelect('61',0);">Ответить</a>         * 
<p onclick="phone.getSelect('67')">- Да, я тебе все переслал. Так в чем проблема, говори толком.</p>


<a id="icons_phone" onclick="icons.showEvent('phone')"></a>




<a class="btn0" onclick="phone.getSelect('61',0)">Ответить</a>


<p onclick="phone.getSelect('67')">- Да, я тебе все переслал. Так в чем проблема, говори толком.</p>


*/
        
        
        
            }
}

?>