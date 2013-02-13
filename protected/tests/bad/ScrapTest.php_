<?php
class ScrapTest extends SeleniumTestCase
{
  function setUp()
  {
      
    $session = $this->webdriver->session('firefox');
    $session->open($this->browser_url);
    
    /*$this->setBrowser("*chrome");
    $this->setBrowserUrl("http://live.skiliks.com/"); */
  }

  public function testMyTestCase()
  {
    $this->open("/");
    $this->type("id=login", "kaaaaav@gmail.com");
    $this->type("id=pass", "111");
    $this->click("css=input.btn");
    $this->click("//input[@value='Начать симуляцию developer']");
    $this->click("id=icons_todo");
    $this->click("css=button");
    $this->click("id=icons_email");
    $this->click("id=mailEmulatorContentDiv");
    $this->click("css=button.btn-cl");
    $this->click("//input[@value='SIM стоп']");
    $this->click("//input[@value='Выход']");
  }
}


