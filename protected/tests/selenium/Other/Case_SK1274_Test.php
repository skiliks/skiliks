<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 3/21/13
 * Time: 2:05 PM
 * To change this template use File | Settings | File Templates.
 */

class Case_SK1274_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * test_SK1274_Case() тестирует задачу SKILIKS-1274
     *
     * 1.
     */
    public function test_SK1274_Case1() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "57");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('E3',"xpath=(//*[contains(text(),'Ох, Иван, раз такое дело, может, перенесем')])",'click');

        //$this->optimal_click("xpath=(//*[contains(text(),'Ох, Иван, раз такое дело, может, перенесем')])");
        sleep(10);
        $this->optimal_click("xpath=(//*[contains(text(),'Правда? И у вас бюджет? Я от него устал')])");
        sleep(10);
        $this->optimal_click("xpath=(//*[contains(text(),'Да, об этом я и не подумал. Ладно, все')])");
        sleep(10);

        $this->assertTrue($this->is_it_done("css=li.icon-active.phone a"));

        $this->no_reply_call();

        sleep(40);

        $this->no_reply_call();

        $this->click("css=input.btn.btn-simulation-stop");
    }
}