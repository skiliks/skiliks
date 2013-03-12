<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты (негативные) на тестирование флага F4 (для SK1413)
 */
class F4_SK1413_N_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

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
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно. Я понял. Сделаю сам.')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        $this->assertTrue($this->verify_flag('F4','0'));

        $this->run_event('ET1.3.1');

        $hours = $this->transfer_time(10);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
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
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно. Я понял. Сделаю сам.')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        $this->assertTrue($this->verify_flag('F4','0'));

        $this->run_event('ET1.3.2');

        $hours = $this->transfer_time(10);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
    }

    /**
     * testSK1413_N_Case3() тестирует задачу SKILIKS-1413
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Ладно. Я понял. Сделаю сам..."
     * 3. Отправляем письмо MS22
     * 4. Проверяем, что флаг F4 не поменялся
     * 5. Запускаем ET1.3.3
     * 6. Переводим время на 10 минут вперед (так как ET1.3.3 происходит с задержкой в 10 минут)
     * 7. Проверяем, что телефон не звонит (т.к. F4=0)
     * 8. Заканчиваем симуляцию
     */
    public function testSK1413_N_Case3() {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно. Я понял. Сделаю сам.')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        $this->assertTrue($this->verify_flag('F4','0'));

        $this->run_event('ET1.3.3');

        $hours = $this->transfer_time(10);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
    }
}