<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на проверку задержки для события, которое было отложено из-за невозможности одновременного запуска(для SK1274)
 */
class DelayForEvent_SK1274_Test extends SeleniumTestHelper
{
    public function test_DelayForEvent_SK1274()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(10); // ждем, когда создается очередь из событий
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "58");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('E3',"xpath=(//*[contains(text(),'Ох, Иван, раз такое дело, может, перенесем')])",'click');
        sleep(10);
        $this->optimal_click("xpath=(//*[contains(text(),'Правда? И у вас бюджет? Я от него устал')])");
        sleep(10);
        $this->optimal_click("xpath=(//*[contains(text(),'Да, об этом я и не подумал. Ладно, все')])");
        sleep(10);
        $this->assertTrue($this->isElementPresent(Yii::app()->params['test_mappings']['icons']['phone']));
        $this->no_reply_call();
        $this->simulation_stop();
    }
}