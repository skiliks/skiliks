<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки наличия шестеренок для настройки звука в окнах (для SK3063))
 */
class WindowHasCogwheel_SK3063_Test extends SeleniumTestHelper
{
    public function test_WindowHasCogwheel_SK3063 ()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("WindowHasCogwheel_SK3063_Test");
        // открыть план
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['todo']);
        sleep(2);
        // проверить, что настроек нет
        $this->assertFalse($this->isElementPresent(Yii::app()->params['test_mappings']['icons']['settings']));
        $this->click(Yii::app()->params['test_mappings']['icons']['close1']);
        // открыть телефон и проверить что настройки есть
        $this->has_settings(Yii::app()->params['test_mappings']['icons']['phone']);
        // открыть почту и проверить что настройки есть
        $this->has_settings(Yii::app()->params['test_mappings']['icons']['mail']);
        // открыть документы и проверить что настроек нет
        $this->has_not_settings(Yii::app()->params['test_mappings']['icons']['documents']);

        $this->clearEventQueueBeforeEleven('RST1');
        // открыть телефон во время звонков и проверить что в тех интерфейсах тоже есть настройки
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['trutnev'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[3]");
        $this->optimal_click("xpath=(//*[contains(text(),'Я по поводу задания от логистов')])");
        $this->assertTrue($this->isVisible(Yii::app()->params['test_mappings']['icons']['settings']));
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'сейчас поговорю с ним и уточню задание')])");
        // открыть написание письма и проверить что в новых интерфейсах настройки есть
        $this->write_email();
        $this->assertTrue($this->isVisible(Yii::app()->params['test_mappings']['icons']['settings']));

        $this->simulation_stop();
    }

    private function has_settings($window)
    {
        $this->optimal_click($window);
        sleep(2);
        $this->assertTrue($this->isElementPresent(Yii::app()->params['test_mappings']['icons']['settings']));
        $this->click(Yii::app()->params['test_mappings']['icons']['close']);
    }

    private function has_not_settings($window)
    {
        $this->optimal_click($window);
        sleep(2);
        $this->assertFalse($this->isElementPresent(Yii::app()->params['test_mappings']['icons']['settings']));
        $this->click(Yii::app()->params['test_mappings']['icons']['close']);
    }


}