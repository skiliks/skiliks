<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест по флагу F30.
 * Сase 1. Запускаем S11, не отвечаем на звонок производственника.
 * Убеждаемся что F2=1. Дожидаемся злую Денежную,
 * Case 2. Запускаем S11, отвечаем на звонок. Убеждаемся что F2=0
 */
class F30_SK1429_Tests extends SeleniumTestHelper
{
    protected function setUp()
{
    $this->setBrowser('firefox');
    $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
    parent::setUp();
}
    public function testSK1429()
    {
    //$this->markTestIncomplete();
    $this->start_simulation();
    $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
    $this->optimal_click(Yii::app()->params['test_mappings']['mail']['new_letter']);
    $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
    $this->waitForElementPresent(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
    $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
    $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);

    $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
    $this->optimal_click("xpath=(//*[contains(text(),'Срочно жду бюджет логистики')])");

    $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
    $this->click(Yii::app()->params['test_mappings']['mail']['send']);

    $this->assertTrue($this->verify_flag('F30','1'));

    $this->optimal_click("css=li.icon-active.mail a");
    $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
    sleep(2);
    $this->verifyTextPresent("Привет, Алексей! Проверяю. Как будет готов - перешлю. \nУдачи, Трудякин");
    }
}