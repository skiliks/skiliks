<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест по флагу F2.
 * Сase 1. Запускаем S11, не отвечаем на звонок производственника.
 * Убеждаемся что F2=1. Дожидаемся злую Денежную,
 * Case 2. Запускаем S11, отвечаем на звонок. Убеждаемся что F2=0
 */
class F2_SK1405_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1405()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('S11', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $this->assertTrue($this->verify_flag('F2','1'));

        $hours = $this->transfer_time(9);

        $this->waitForVisible("xpath=(//*[contains(text(),'У меня нет слов от возмущения')])");
        $this->assertTextPresent("У меня нет слов от возмущения");
    }

    public function testSK1405_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('S11', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, файл готовил не я')])");
        $this->assertFalse($this->verify_flag('F2','1'));
    }
}