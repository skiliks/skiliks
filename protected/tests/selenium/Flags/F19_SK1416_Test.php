<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для флага F19 (для SK1416)
 */
class F19_SK1416_Test extends SeleniumTestHelper
{
    /**
     * testSK1416_Case1() тестирует задачу SKILIKS-1416
     *
     * 1. Проверяем, что флаг F19=0
     * 2. Запускаем Е3
     * 3. Запуск диалога
     * 4. Идем по диалогу до фразы "Спасибо тебе, значит, через две недели"
     * 5. Проверяем, что значение флага F19=1
     */
    public function testSK1416_Case1()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(10);
        $this->assertTrue($this->verify_flag('F19','0'));
        $this->run_event('E3',"xpath=(//*[contains(text(),'Приветствую, это Иван Доброхотов.')])",'-');
        $this->optimal_click("xpath=(//*[contains(text(),'Ох, Иван, раз такое дело, может,')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Спасибо тебе, значит, через две недели')])");
        $this->assertTrue($this->verify_flag('F19','1'));
        $this->simulation_stop();

    }

    /**
     * testSK1416_Case2() тестирует задачу SKILIKS-1416
     *
     * 1. Проверяем, что флаг F19=0
     * 2. Запускаем Е3
     * 3. Звонит телефон -> Отвечаем
     * 4. Идем по диалогу до фразы "Вот и стимул все вовремя"
     * 5. Проверяем, что значение флага F19=1
     */
    public function testSK1416_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(10);
        $this->assertTrue($this->verify_flag('F19','0'));
        $this->run_event('E3',"xpath=(//*[contains(text(),'Приветствую, это Иван Доброхотов.')])",'-');
        $this->optimal_click("xpath=(//*[contains(text(),'Ох, Иван, раз такое дело, может,')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Правда? И у вас бюджет?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Вот и стимул все вовремя')])");
        $this->assertTrue($this->verify_flag('F19','1'));
        $this->simulation_stop();
    }

    /**
     * testSK1416_Case3() тестирует задачу SKILIKS-1416
     *
     * 1. Проверяем, что флаг F19=0
     * 2. Запускаем T2
     * 3. Звонит телефон -> Отвечаем
     * 4. Идем по диалогу до фразы "Послушай, Иван, а мы можем встретиться,"
     * 5. Проверяем, что значение флага F19=1
     */
    public function testSK1416_Case3()
    {
        $this->start_simulation();
        sleep(10);
        $this->assertTrue($this->verify_flag('F19','0'));
        $this->run_event('T2',"xpath=(//*[contains(text(),'Доброхотов! Слушаю!')])",'-');
        $this->optimal_click("xpath=(//*[contains(text(),'Иван, привет! Это Федоров.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Послушай, Иван, а мы можем встретиться, ')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Ну прямо гора с плеч! Я сегодня в')])");
        $this->assertTrue($this->verify_flag('F19','1'));
        $this->simulation_stop();
    }

    /**
     * testSK1416_N_Case1() тестирует задачу SKILIKS-1416
     *
     * 1. Установить флаг F19=1
     * 2. Запускаем E3
     * 3. Проверить, что в течении 10 игровых минут ничего не произошло
     */
    public function testSK1416_N_Case1()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->optimal_click("link=F19");
        $this->assertTrue($this->verify_flag('F19','1'));
        $this->run_event('ET3.1',"xpath=(//*[contains(text(),'октября')])",'-');
        sleep(5);
        $this->assertFalse($this->isVisible(Yii::app()->params['test_mappings']['active_icons']['phone']));
        $this->simulation_stop();
    }
}
