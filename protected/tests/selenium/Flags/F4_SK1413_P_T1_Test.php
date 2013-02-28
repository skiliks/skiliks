<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 2/28/13
 * Time: 2:49 AM
 * To change this template use File | Settings | File Templates.
 */

class F4_SK1413_P_T1_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1413_PT1() {
        // next line for not running the test
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->type("id=addTriggerSelect", "E1.3");
        $this->waitForVisible("xpath=//form/fieldset/div[2]/div/input[2]");
        $this->click("xpath=//form/fieldset/div[2]/div/input[2]");

        $this->waitForVisible("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл')])");
        $this->click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл')])");

        $this->waitForVisible(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);
        $this->click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        sleep(5);
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[8]","1");

       // to make changes with time
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "11");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "50");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->type("id=addTriggerSelect", "ET1.3.1");
        $this->waitForVisible("xpath=//form/fieldset/div[2]/div/input[2]");
        $this->click("xpath=//form/fieldset/div[2]/div/input[2]");

        $this->optimal_click("css=li.icon-active.phone a");
        $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

       /* while(($this->verifyTextPresent("Аналитик"))==false)
        {
            $this->optimal_click("css=li.icon-active.phone a");
            sleep(10);
            if (($this->verifyTextPresent("Аналитик"))==true)
            {
                $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
                $this->click(Yii::app()->params['test_mappings']['phone']['reply']);
                sleep(2);
                break;
            }
            else
            {
                $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['no_reply']);
                $this->click(Yii::app()->params['test_mappings']['phone']['no_reply']);
                sleep(2);
            }
        }*/


        $this->waitForVisible("xpath=(//*[contains(text(),'Господи, да ведь там в вашем бюджете')])");
        $this->assertTextPresent("Господи, да ведь там в вашем бюджете");

        $this->click("css=input.btn.btn-simulation-stop");
    }
}

