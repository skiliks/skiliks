<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки активных вкладок в мейл-клиенте
 */
class MailInsetsActive_SK3363_Test extends SeleniumTestHelper
{
    public function test_MailInsetsActive_SK3363()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(3);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(20);
        $this->assertTrue($this->getText("xpath=//li[contains(@class, 'active')]/label")=="Входящие");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['draft']);
        sleep(10);
        printf($this->getText("xpath=//li[contains(@class, 'active')]/label"));
        $this->assertTrue($this->getText("xpath=//li[contains(@class, 'active')]/label")=="Черновики");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        sleep(2);
        $this->assertTrue($this->getText("xpath=//li[contains(@class, 'active')]/label")=="Исходящие");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['trash']);
        sleep(2);
        $this->assertTrue($this->getText("xpath=//li[contains(@class, 'active')]/label")=="Корзина");

        $this->simulation_stop();
    }
}