<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки что можно открыть два окна документов одновременно (для SK3362))
 */
class TwoDocumentsOpened_SK3362_Test extends SeleniumTestHelper
{
    public function test_TwoDocumentsOpened_SK3362 ()
    {
        $this->start_simulation("TwoDocumentsOpened_SK3362_Test");

        $this->clearEventQueueBeforeEleven('RST1');

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['documents']);
        sleep(10);
        $this->mouseDown("xpath=//*[@id='Бюджет АО_2013.xls']/div[1]");
        $this->click("xpath=//*[@id='Бюджет АО_2013.xls']/div[1]");
        $this->doubleClick("xpath=//*[@id='Бюджет АО_2013.xls']/div[1]");
        sleep(5);
        $this->assertTrue($this->isVisible("css=#cell_B4"));

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['documents']);
        $this->mouseDown("xpath=//*[@id='Бюджет производства_2013_утв.xls']/div[1]");
        $this->click("xpath=//*[@id='Бюджет производства_2013_утв.xls']/div[1]");
        $this->doubleClick("xpath=//*[@id='Бюджет производства_2013_утв.xls']/div[1]");
        sleep(5);
        $this->assertTrue($this->isVisible("css=#cell_B7"));

        $this->simulation_stop();
    }
}