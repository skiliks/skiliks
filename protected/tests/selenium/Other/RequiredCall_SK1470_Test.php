<?php
    /**
     * \addtogroup Selenium
     * @{
     */
    /**
     * Ответ на звонок, на который нельзя не ответить.
     * 1 Кейс. Запускаем ивент. Кликаем по входящему, отвечаем. Проверяем первую ответную реплику
     * 2 Кейс. Запускаем ивент.Дожидаемся пока трубка автоматически подымется, проверяем первую ответную реплику
     */
class RequiredCall_SK1470_Test extends SeleniumTestHelper
{

    public function test_RequiredCall_SK1470_Case1()
    {
        $this->start_simulation();
        $this->run_event('ET2.4', Yii::app()->params['test_mappings']['phone']['reply'], '-');
        $this->assertElementNotPresent(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $this->click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно, Валерий Семенович! Буду у Вас в 16.00 с готовой презентаций')])");
        $this->close();
    }

    public function test_RequiredCall_SK1470_Case2()
    {
        $this->start_simulation();
        $this->run_event('ET2.4',Yii::app()->params['test_mappings']['phone']['reply'],'-');
        $this->assertElementNotPresent(Yii::app()->params['test_mappings']['phone']['no_reply']);
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно, Валерий Семенович! Буду у Вас в 16.00 с готовой презентаций')])");
        $this->close();
    }
}