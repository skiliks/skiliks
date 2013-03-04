<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 2/28/13
 * Time: 6:47 PM
 * To change this template use File | Settings | File Templates.
 */

class F4_SK1413_P_T3_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1413_PT3() {
        // next line for not running the test
        $this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        sleep(5);
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[8]","1");

        $this->run_event('ET1.3.2');

        // to make changes with time
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "12");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "15");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->optimal_click("css=li.icon-active.phone a");
        $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

        $this->waitForVisible("xpath=(//*[contains(text(),'Господи, и что же мне теперь делать')])");
        $this->assertTextPresent("Господи, и что же мне теперь делать");

        $this->click("css=input.btn.btn-simulation-stop");
    }
}

