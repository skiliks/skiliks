<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 3/4/13
 * Time: 9:57 PM
 * To change this template use File | Settings | File Templates.
 */
class F4_SK1413_N_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1413_N_Case1() {
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

        $this->run_event('ET1.3.1');

        $hours = $this->transfer_time(10);
        print ($hours[0]);
        print (" : ");
        print ($hours[1]);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");

        $this->click("css=input.btn.btn-simulation-stop");
    }

    public function testSK1413_N_Case2() {
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

        $this->run_event('ET1.3.2');

        $hours = $this->transfer_time(10);
        print ($hours[0]);
        print (" : ");
        print ($hours[1]);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
    }

    public function testSK1413_N_Case3() {
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

        $this->run_event('ET1.3.3');

        $hours = $this->transfer_time(10);
        print ($hours[0]);
        print (" : ");
        print ($hours[1]);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
    }
}