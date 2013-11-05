<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки наличия списка всех входящих и исходящих сообщений
 */
class StartMessageList_SK3360_Test extends SeleniumTestHelper
{
    public function test_StartMessageList_SK3360()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("StartMessageList_SK3360_Test");

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(5);
        $this->assertTextPresent("Вниманию руководителей");
        $this->assertTextPresent("Форма отчетности для производства");
        $this->assertTextPresent("По ценовой политике");
        $this->assertTextPresent("Трудовой договор");
        $this->assertTextPresent("Новая система мотивации");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        sleep(5);
        $this->assertTextPresent("Отчет для Правления");
        $this->assertTextPresent("Задача по запросу логистов");

        $this->simulation_showLogs();
    }
}