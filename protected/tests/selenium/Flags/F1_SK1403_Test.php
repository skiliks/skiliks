<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vad
 * Date: 03.03.13
 * Time: 22:29
 * To change this template use File | Settings | File Templates.
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

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "09");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "11");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

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