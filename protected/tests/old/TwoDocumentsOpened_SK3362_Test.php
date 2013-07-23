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
        $this->start_simulation();
        //$this->clearEventQueueBeforeEleven('RST1');
        sleep(160);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['documents']);
        sleep(10);
        $this->doubleClick("xpath=//*[@id='Бюджет АО_2013.xls']");
        sleep(5);
        $this->assertTrue($this->isVisible("css=#cell_B4"));
        //$this->click(Yii::app()->params['test_mappings']['icons']['close1']);
        $this->simulation_stop();
    }
}