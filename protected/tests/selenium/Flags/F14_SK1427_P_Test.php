<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для флага F14 (для SK1427)
 */
class F14_SK1427_P_Test extends SeleniumTestHelper
{
    /**
     * testSK1427_Case1() тестирует задачу SKILIKS-1427 для статуса MAIL
     *
     * 1. Проверяем, что флаг F14 = 0
     * 2. Запускаем E2.4
     * 3. Кликаем по диалогу до фразы "Отлично, одной проблемой меньше"
     * 4. Проверяем, что флаг F14 изменился (F14 = 1)
     * 5. Устанавливаем время на 15:30
     * 6. Загружаем все письма, которые должны прийти на момент времени 15:30
     * 7. Окрываем почту и проверяем, что нужное письмо с темой "Презентация для ГД_итог"
     */
    public function testSK1427_Case1()
    {
        $this->start_simulation();
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])",'-');
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше')])");

        $this->assertTrue($this->verify_flag('F14','1'));

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "15");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "30");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        // downloading all messages
        sleep(30);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->isTextPresent("Презентация для ГД_итог"));
        $this->simulation_stop();
    }
}