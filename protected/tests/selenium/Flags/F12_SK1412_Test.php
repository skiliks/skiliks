<?php
class F12_SK1412_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1412()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('ET3.2');
        $this->optimal_click("css=li.icon-active.door a");
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['deny']);

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[1]/tbody/tr/td[4]");

        $this->assertTrue($this->verify_flag('F12','1'));

        $this->transfer_time(9);

        $this->waitForVisible("xpath=(//*[contains(text(),'У меня нет слов от возмущения')])");
        $this->assertTextPresent("У меня нет слов от возмущения");
    }

    public function testSK1412_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('ET3.2');
        $this->optimal_click("css=li.icon-active.door a");
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);
        $this->optimal_click("xpath=(//*[contains(text(),'Сделал, прямо перед тобой отправил')])");

        $this->assertTrue($this->verify_flag('F12','0'));
    }
}