<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест по флагу F4.
 * Сase 1. Запускаем S9, не отвечаем на звонок производственника. Дожидаемся злую Денежную, убеждаемся что F1=1
 * Case 2. Запускаем S9, отвечаем на звонок производственника. Убеждаемся что F1=0
 */
class F1_SK1403_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1403()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('S9');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[1]/tbody/tr/td[1]");

        $this->assertTrue($this->verify_flag('F1','1'));

        $hours = $this->transfer_time(9);

        $this->waitForVisible("xpath=(//*[contains(text(),'У меня нет слов от возмущения')])");
        $this->assertTextPresent("У меня нет слов от возмущения");
    }

    public function testSK1403_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('S9');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, вопрос в чем')])");

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[1]/tbody/tr/td[1]");
        $this->assertFalse($this->verify_flag('F1','1'));
    }
}