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
    
        # вот таким извращенным способом вводится текст
    $this->waitForElement($session, "id","login")->value(
           array("value"=>str_split("kaaaaav@gmail.com"))
         );
    
         
         # Ждём появления елемента
       
    $this->waitForElement($session, "id","pass")->value(array("value"=>str_split("111")));
    
    $session->element("xpath", "//input[@class='btn']")->click();
    
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
    
    $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию developer']");
    $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
    
        # ожидание одного из компонентов (чтобы убедиться что симуляция стартовала)
    $this->waitForElement($session, 'xpath', '//a[@id="icons_documents"]');
          
        #старт теста по параметрам заданным Антоном
      
      $this->waitForElement($session, "id","addTriggerSelect")->value(array("value"=>str_split("ET1.1")));
      $this->waitForElement($session, "id","addTriggerDelay")->value(array("value"=>str_split("1")));
     
      $session->element("xpath", "//input[@value='Создать']")->click();
      
      //$session->verifyTextPresent("Событие было успешно добавлено");
      sleep(3);
      $session->element('css selector','.alert a.btn')->click();
      
      
           
      $session->element ('xpath', '//a[@id="icons_documents"]')->click();
      sleep(5);
      
    sleep(5);
    $session->element("xpath", "//input[@value='Создать']")->click();
    
    $this->click("id=icons_phone");
    $this->click("link=ПРИНЯТЬ");
    $this->click("//p[@onclick=\"phone.getSelect('6')\"]");
    $this->click("css=li > p");
    $this->click("css=li > p");
    $this->click("id=addTriggerSelect");
    $this->type("id=addTriggerSelect", "ET2.1");
    $this->click("css=form.well > input.btn");
    $this->click("link=Ок");
    $this->click("id=icons_phone");
    $this->click("link=ОТКЛОНИТЬ");
    $this->click("id=icons_phone");
    $this->click("link=ПРИНЯТЬ");
    $this->click("css=li > p");
    $this->click("//p[@onclick=\"phone.getSelect('150')\"]");
    $this->click("id=addTriggerMainDiv");
    $this->verifyTextPresent("4.66666666666667");
    $this->click("id=addAssessmentMainForm");
    $this->verifyTextPresent("Сумма оценок: 4.66666666666667");
    $this->verifyTextPresent("Сумма оценок 6x: 4");
    $this->verifyTextPresent("Сумма оценок Negative: 0");
  }
}

