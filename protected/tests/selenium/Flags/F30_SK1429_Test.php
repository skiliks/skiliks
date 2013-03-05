<?php
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
    $this->select("css=select.origin", "Срочно жду бюджет логистики");
    $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
    $this->click(Yii::app()->params['test_mappings']['mail']['send']);

    $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[6]");
    #TODO: заменить!
    sleep(10);
    $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[6]","1");

    $this->optimal_click("css=li.icon-active.mail a");
    $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
    $this->optimal_click("//table[@id='mlTitle']/tbody/tr[5]/td[2]");
    $this->verifyTextPresent("Привет, Алексей! Проверяю. Как будет готов - перешлю. \nУдачи, Трудякин");
    }
}