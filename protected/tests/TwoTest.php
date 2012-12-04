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
        
        #добавление события
     $this->waitForElement($session, "id","addTriggerSelect")->value(array("value"=>str_split("E1")));
     $this->waitForElement($session, "id","addTriggerDelay")->value(array("value"=>str_split("1")));
        #клик на "создать событие"
     $session->element("xpath", "//input[@value='Создать']")->click();
        #ждем исчезновения черной полосы загрузки и в появившемся окне тригера кликаем ОК
      sleep(3);
      $session->element('css selector','.alert a.btn')->click();
      
        #2 ответа на диалог с Денежной
      sleep(15);
      $session->element("xpath", "//p[@onclick=\"phone.getSelect('5')\"]")->click();
      sleep(3);
      $session->element("xpath", "//p[@onclick=\"phone.getSelect('11')\"]")->click();
      sleep(2);
      
        #обнуляем поле ввода названия события, вписываем новое, кликаем создать
      $session->element("id", "addTriggerSelect")->clear();
      $this->waitForElement($session, "id","addTriggerSelect")->value(array("value"=>str_split("E8.3")));
      $session->element("xpath", "//input[@value='Создать']")->click();
        #подтвеждение тригера
      sleep(3);
      $session->element('css selector','.alert a.btn')->click();
      sleep(15);
      
       #3 ответа на диалог с начальником АйТи отдела за обедом
      $session->element("xpath", "//p[@onclick=\"dialogController.getSelect('332')\"]")->click();
      sleep(3);
      $session->element("xpath", "//p[@onclick=\"dialogController.getSelect('337')\"]")->click();
      sleep(3);
      $session->element("xpath", "//p[@onclick=\"dialogController.getSelect('340')\"]")->click();
      sleep(10);
      
       #обнуляем поле ввода названия события, вписываем новое, кликаем создать
      $session->element("id", "addTriggerSelect")->clear();
      $this->waitForElement($session, "id","addTriggerSelect")->value(array("value"=>str_split("E12.1")));
      $session->element("xpath", "//input[@value='Создать']")->click();
        #подтвеждение тригера
      sleep(3);
      $session->element('css selector','.alert a.btn')->click();
      sleep(15);
      
        #3 ответа на диалог
      $session->element("xpath", "//p[@onclick=\"phone.getSelect('442')\"]")->click();
      sleep(3);
      $session->element("xpath", "//p[@onclick=\"phone.getSelect('444')\"]")->click();
      sleep(3);
      $session->element("xpath", "//p[@onclick=\"phone.getSelect('448')\"]")->click();
      sleep(10);
      
        #проверка конечного результата
      $this->assertEquals("Сумма оценок: 4.5", $session->element("css selector",".result-total")->text()); 
      $this->assertEquals("Сумма оценок 6x: 10", $session->element("css selector",".result-total-6x")->text()); 
      $this->assertEquals("Сумма оценок Negative: 0", $session->element("css selector",".result-total-negative")->text()); 
      
  }
}
?>