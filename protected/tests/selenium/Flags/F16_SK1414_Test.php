<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на тестирование флага F16 (для SK1414)
 */
class F16_SK1414_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * testSK1414_Case1() тестирует задачу SKILIKS-1414
     *
     * 1. Проверяем, что флаг F16 = 0
     * 2. Запускаем E2.4
     * 3. Кликаем по диалогу (2->2)
     * 4. Проверяем, что флаг F16 изменился (F16 = 1)
     * 5. Устанавливаем время на 13:29
     * 6. Загружаем все письма, которые должны прийти на момент времени 13:30
     * 7. Окрываем почту и проверяем, что нужное письмо с темой "Презентация для ГД_рабочая версия"
     */
    public function testSK1414_Case1()
    {
        // tests to checking the actions before F16 = 1
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(2);
        $this->assertTrue($this->verify_flag('F16','0'));

        $this->run_event('E2.4');

        $this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, привет! Что там с презентацией')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это хорошо, что задача ясна.')])");

        $this->assertTrue($this->verify_flag('F16','1'));

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "13");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "30");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        // downloading all messages
        sleep(20);
        $this->optimal_click("css=li.icon-active.mail a");
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("Презентация для ГД_рабочая версия"));

    }

}
