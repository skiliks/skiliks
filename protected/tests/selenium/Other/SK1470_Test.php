<?php
    /**
     * \addtogroup Selenium
     * @{
     */
    /**
     * Ответ на звонок, на который нельзя не ответить.
     * 1 Кейс. Запускаем ивент. Кликаем по входящему, отвечаем. Проверяем первую ответную реплику
     * 2 Кейс. Запускаем ивент.
     * Дожидаемся пока трубка автоматически подымется, проверяем первую ответную реплику
     */
class SK1470_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1470_Case1()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('ET2.4');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->assertTrue($this->isVisible(Yii::app()->params['test_mappings']['phone']['reply']));
        $this->assertFalse($this->isElementPresent(Yii::app()->params['test_mappings']['phone']['no_reply']));
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно, Валерий Семенович! Буду у Вас в 16.00 с готовой презентаций')])");
    }

    public function testSK1470_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('ET2.4');
        $this->waitForVisible((Yii::app()->params['test_mappings']['phone']['reply']));
        $this->assertFalse($this->isElementPresent(Yii::app()->params['test_mappings']['phone']['no_reply']));
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно, Валерий Семенович! Буду у Вас в 16.00 с готовой презентаций')])");
    }
}