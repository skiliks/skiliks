<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 3/19/13
 * Time: 1:48 PM
 * To change this template use File | Settings | File Templates.
 */
class prob_test extends SeleniumTestHelper
{
    public function prob_test() {

        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4');

        $this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай мы все-таки посмотрим, что у')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Пересылаю. Но смысл-то какой? Это')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что такое?! Так ведь она пустая!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хотелось бы знать')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Если бы вы не отвлекали меня по')])");
        $this->optimal_click("xpath=(//*[contains(text(),'У меня нет уверенности, что ты сделаешь')])");


        $this->assertTrue($this->verify_flag('F14','0'));

        // tests to checking the actions after F14 = 1 (REPLICA)
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "15");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "50");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        // в 15:50 запускается ET12.1
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->transfer_time(10);
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);

        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович сказал, что презентация не готова')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А чего там дорабатывать?')])");
    }
}
