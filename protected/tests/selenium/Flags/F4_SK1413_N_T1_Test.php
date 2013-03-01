<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 2/28/13
 * Time: 7:02 PM
 * To change this template use File | Settings | File Templates.
 */
class F4_SK1413_N_T1_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1413_NT1() {
        // next line for not running the test
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно. Я понял. Сделаю сам.')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        sleep(5);
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[8]","0");

        $this->run_event('E1.3.1');

        // to make changes with time
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "09");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "20");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->verifyVisible(Yii::app()->params['test_mappings']['icons']['phone']);

        $this->click("css=input.btn.btn-simulation-stop");
    }
}