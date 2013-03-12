<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты (позитивные) на тестирование флага F4 (для SK1413)
 */
class F4_SK1413_P_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * testSK1413_P_Case1() тестирует задачу SKILIKS-1413
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Я тебе сейчас перешлю файл..."
     * 3. Отправляем письмо MS22
     * 4. Проверяем, что флаг F4 поменялся (F4=1)
     * 5. Запускаем ET1.3.1
     * 6. Проверяем, что телефон звонит (т.к. F4=1)
     * 7. Если телефон звонит, то отвечаем на звонок
     * 8. Проверяем, что требуемая фраза "Господи, да ведь там в вашем бюджете" появилась
     * 9. Заканчиваем симуляцию
     */
    public function testSK1413_P_Case1() {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->run_event('ET1.3.1');

        $hours = $this->transfer_time(0);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            $this->click("css=li.icon-active.phone a");
            $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
            $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

            $this->waitForVisible("xpath=(//*[contains(text(),'Господи, да ведь там в вашем бюджете')])");
            $this->assertTextPresent("Господи, да ведь там в вашем бюджете");
        }
        else
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
    }

    /**
     * testSK1413_P_Case2() тестирует задачу SKILIKS-1413
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Однако тебе все-таки..."
     * 3. Отправляем письмо MS22
     * 4. Проверяем, что флаг F4 поменялся (F4=1)
     * 5. Запускаем ET1.3.1
     * 6. Проверяем, что телефон звонит (т.к. F4=1)
     * 7. Если телефон звонит, то отвечаем на звонок
     * 8. Проверяем, что требуемая фраза "Господи, да ведь там в вашем бюджете" появилась
     * 9. Заканчиваем симуляцию
     */
    public function testSK1413_P_Case2() {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->run_event('ET1.3.1');

        $hours = $this->transfer_time(0);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            $this->optimal_click("css=li.icon-active.phone a");
            $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
            $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

            $this->waitForVisible("xpath=(//*[contains(text(),'Господи, да ведь там в вашем бюджете')])");
            $this->assertTextPresent("Господи, да ведь там в вашем бюджете");

            $this->click("css=input.btn.btn-simulation-stop");
        }
        else
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
    }

    /**
     * testSK1413_P_Case3() тестирует задачу SKILIKS-1413
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Я тебе сейчас перешлю файл..."
     * 3. Отправляем письмо MS22
     * 4. Проверяем, что флаг F4 поменялся (F4=1)
     * 5. Запускаем ET1.3.2
     * 6. Проверяем, что телефон звонит (т.к. F4=1)
     * 7. Если телефон звонит, то отвечаем на звонок
     * 8. Проверяем, что требуемая фраза "Господи, и что же мне теперь делать" появилась
     * 9. Заканчиваем симуляцию
     */
    public function testSK1413_P_Case3() {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->run_event('ET1.3.2');

        $hours = $this->transfer_time(0);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            $this->optimal_click("css=li.icon-active.phone a");
            $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
            $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

            $this->waitForVisible("xpath=(//*[contains(text(),'Господи, и что же мне теперь делать')])");
            $this->assertTextPresent("Господи, и что же мне теперь делать");

            $this->click("css=input.btn.btn-simulation-stop");
        }
        else
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
    }

    /**
     * testSK1413_P_Case4() тестирует задачу SKILIKS-1413
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Однако тебе все-таки..."
     * 3. Отправляем письмо MS22
     * 4. Проверяем, что флаг F4 поменялся (F4=1)
     * 5. Запускаем ET1.3.2
     * 6. Проверяем, что телефон звонит (т.к. F4=1)
     * 7. Если телефон звонит, то отвечаем на звонок
     * 8. Проверяем, что требуемая фраза "Господи, и что же мне теперь делать" появилась
     * 9. Заканчиваем симуляцию
     */
    public function testSK1413_P_Case4() {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['send_message_quickly']['MS22']);

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->run_event('ET1.3.2');

        $hours = $this->transfer_time(0);

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            $this->optimal_click("css=li.icon-active.phone a");
            $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
            $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

            $this->waitForVisible("xpath=(//*[contains(text(),'Господи, и что же мне теперь делать')])");
            $this->assertTextPresent("Господи, и что же мне теперь делать");

            $this->click("css=input.btn.btn-simulation-stop");
        }
        else
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");
    }
}
/**
 * @}
 */