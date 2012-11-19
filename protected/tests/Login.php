<?php
class Login extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*firefox");
    $this->setBrowserUrl("http://front.skiliks.com/");
  }

  public function testMyTestCase()
  {

$this->verifyTextPresent("email");
$this->verifyTextPresent("Пароль");
$this->verifyTextPresent("");
$this->verifyTextPresent("");
try {
$this->assertTrue((bool)preg_match('/^exact:Забыли пароль[\s\S]$/',$this->getValue("css=div.world-index-b2Div > input.btn")));
} catch (PHPUnit_Framework_AssertionFailedError $e) {
array_push($this->verificationErrors, $e->toString());
}
$this->type("id=login", "kaaaaav@gmail.com");
$this->type("id=pass", "111");
$this->click("css=input.btn");
try {
$this->assertEquals("Начать симуляцию promo", $this->getValue("css=input.btn"));
} catch (PHPUnit_Framework_AssertionFailedError $e) {
array_push($this->verificationErrors, $e->toString());
}
try {
$this->assertEquals("Начать симуляцию developer", $this->getValue("//input[@value='Начать симуляцию developer']"));
} catch (PHPUnit_Framework_AssertionFailedError $e) {
array_push($this->verificationErrors, $e->toString());
}
try {
$this->assertEquals("Изменить личные данные", $this->getValue("//input[@value='Изменить личные данные']"));
} catch (PHPUnit_Framework_AssertionFailedError $e) {
array_push($this->verificationErrors, $e->toString());
}
try {
$this->assertEquals("Выход", $this->getValue("//input[@value='Выход']"));
} catch (PHPUnit_Framework_AssertionFailedError $e) {
array_push($this->verificationErrors, $e->toString());
}
$this->click("//input[@value='Начать симуляцию developer']");
  }
}
?>