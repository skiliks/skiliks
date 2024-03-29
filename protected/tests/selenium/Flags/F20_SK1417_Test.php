<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для флага F20 (для SK1417)
 */
class F20_SK1417_Test extends SeleniumTestHelper
{
    /**
     * testSK1417_Case1() тестирует задачу SKILIKS-1417
     *
     * 1. Проверяем, что флаг F20=0
     * 2. Запускаем Е3
     * 3. Запуск диалога
     * 4. Идем по диалогу до фразы "Вот спасибо! Выручил! Давай до вечера,"
     * 5. Проверяем, что значение флага F20=1
     */
    public function testSK1417_Case1()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("F20_SK1417_Test_Case_1");
        sleep(10);
        $this->assertTrue($this->verify_flag('F20','0'));
        $this->run_event('E3',"xpath=(//*[contains(text(),'Приветствую, это Иван Доброхотов.')])",'-');
        $this->optimal_click("xpath=(//*[contains(text(),'Ох, Иван, раз такое дело, может,')])");
        $this->optimal_click("xpath=(//*[contains(text(),' Вот спасибо! Выручил! Давай до вечера,')])");
        $this->assertTrue($this->verify_flag('F20','1'));
        $this->simulation_stop();
    }

    /**
     * testSK1417_Case2() тестирует задачу SKILIKS-1417
     *
     * 1. Проверяем, что флаг F20=0
     * 2. Запускаем Е3
     * 3. Звонит телефон -> Отвечаем
     * 4. Идем по диалогу до фразы "Да, об этом я и не подумал. Ладно,"
     * 5. Проверяем, что значение флага F20=1
     */
    public function testSK1417_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("F20_SK1417_Test_Case_2");
        sleep(10);
        $this->assertTrue($this->verify_flag('F20','0'));
        $this->run_event('E3',"xpath=(//*[contains(text(),'Приветствую, это Иван Доброхотов.')])",'-');
        $this->optimal_click("xpath=(//*[contains(text(),'Ох, Иван, раз такое дело, может,')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Правда? И у вас бюджет?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, об этом я и не подумал. Ладно, ')])");
        $this->assertTrue($this->verify_flag('F20','1'));
        $this->simulation_stop();
    }

    /**
     * testSK1417_Case3() тестирует задачу SKILIKS-1417
     *
     * 1. Проверяем, что флаг F20=0
     * 2. Запускаем T2
     * 3. Звонит телефон -> Отвечаем
     * 4. Идем по диалогу до фразы "Давай встретимся вечером,"
     * 5. Проверяем, что значение флага F20=1
     */
    public function testSK1417_Case3()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("F20_SK1417_Test_Case_3");
        sleep(10);
        $this->assertTrue($this->verify_flag('F20','0'));
        $this->run_event('T2',"xpath=(//*[contains(text(),'Доброхотов! Слушаю!')])",'-');
        $this->optimal_click("xpath=(//*[contains(text(),'Иван, привет! Это Федоров.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай встретимся вечером,')])");
        $this->assertTrue($this->verify_flag('F20','1'));
        $this->simulation_stop();
    }
}
