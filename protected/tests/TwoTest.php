<?php
class OneTest extends SeleniumTestCase
{
   public function testMyTestCase()
  {
        # Login
    $session = $this->webdriver->session('firefox');
    $session->open($this->browser_url);
        # раскрыть окно на весь экран
    $session->window()->maximize();
        # из-за черной полосы загрузки, пришлось добавить временное ожидание
        sleep(2);
        # вводится текст
    $this->waitForElement($session, "id","login")->value(
           array("value"=>str_split("kaaaaav@gmail.com"))
         );
        # Ждём появления елемента и кликаем на него
    $this->waitForElement($session, "id","pass")->value(array("value"=>str_split("111")));
        # Кликаем на него
    $session->element("xpath", "//input[@class='btn']")->click();
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
    $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию developer']");
    $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
        # ожидание одного из компонентов (чтобы убедиться что симуляция стартовала)
    $this->waitForElement($session, 'xpath', '//a[@id="icons_documents"]');
        #старт теста по параметрам заданным Антоном
    
    
    

    
    
    $this->type("id=addTriggerSelect", "E1");
    $this->type("id=addTriggerDelay", "1");
    $this->click("css=form.well > input.btn");
    $this->click("link=Ок");
    $this->click("css=p.phone-reply-ch.max");
    $this->click("css=li > p");
    $this->click("//p[@onclick=\"phone.getSelect('11')\"]");
    $this->click("id=addTriggerSelect");
    $this->type("id=addTriggerSelect", "E8.3");
    $this->click("css=form.well > input.btn");
    $this->click("link=Ок");
    $this->click("css=li > p");
    $this->click("//p[@onclick=\"dialogController.getSelect('337')\"]");
    $this->click("css=li > p");
    $this->click("id=addTriggerSelect");
    $this->type("id=addTriggerSelect", "E12.1");
    $this->click("css=form.well > input.btn");
    $this->click("css=p.phone-reply-ch.max");
    $this->click("//p[@onclick=\"phone.getSelect('442')\"]");
    $this->click("css=li > p");
    $this->click("//p[@onclick=\"phone.getSelect('448')\"]");
    $this->verifyTextPresent("Сумма оценок: 4.5");
    $this->verifyTextPresent("Сумма оценок 6x: 10");
    $this->verifyTextPresent("Сумма оценок Negative: 0");
  }
}
?>