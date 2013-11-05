<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты (негативные) для флага F4 (для SK1413)
 */
class F4_SK1413_N_Test extends SeleniumTestHelper
{
    /**
     * testSK1413_N_Case1() тестирует задачу SKILIKS-1413
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Ладно. Я понял. Сделаю сам..."
     * 3. Отправляем письмо MS22
     * 4. Проверяем, что флаг F4 не поменялся
     * 5. Запускаем ET1.3.1
     * 6. Переводим время на 10 минут вперед (так как ET1.3.1 происходит с задержкой в 10 минут)
     * 7. Проверяем, что телефон не звонит (т.к. F4=0)
     * 8. Заканчиваем симуляцию
     */
    public function testSK1413_N_Case1() {
        // $this->markTestIncomplete();
        $this->start_simulation("F4_SK1413_N_Test_Case1");
        $this->optimal_click('link=F32');
        sleep(5);
        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно. Я понял. Сделаю сам.')])");

        $this->assertTrue($this->verify_flag('F4','0'));

        $this->run_event('ET1.3.1');
        sleep(10);
        $this->assertFalse($this->isElementPresent(Yii::app()->params['test_mappings']['icons_active']['phone']));
        $this->simulation_showLogs();
    }

    /**
     * testSK1413_N_Case2() тестирует задачу SKILIKS-1413
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Ладно. Я понял. Сделаю сам..."
     * 3. Отправляем письмо MS22
     * 4. Проверяем, что флаг F4 не поменялся
     * 5. Запускаем ET1.3.2
     * 6. Переводим время на 10 минут вперед (так как ET1.3.2 происходит с задержкой в 10 минут)
     * 7. Проверяем, что телефон не звонит (т.к. F4=0)
     * 8. Заканчиваем симуляцию
     */
    public function testSK1413_N_Case2() {
        // $this->markTestIncomplete();
        $this->start_simulation("F4_SK1413_N_Test_Case2");
        $this->optimal_click('link=F32');
        sleep(5);
        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])",'click');

        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно. Я понял. Сделаю сам.')])");

        $this->assertTrue($this->verify_flag('F4','0'));

        $this->run_event('ET1.3.2');
        sleep(5);
        $this->assertFalse($this->isElementPresent(Yii::app()->params['test_mappings']['icons_active']['phone']));
        $this->simulation_showLogs();
    }
}