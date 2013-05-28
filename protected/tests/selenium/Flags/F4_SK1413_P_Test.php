<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты (позитивные) для флага F4 (для SK1413)
 */
class F4_SK1413_P_Test extends SeleniumTestHelper
{
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
        $this->optimal_click('link=F32');
        sleep(5);
        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл')])");
        sleep(10);

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->run_event('ET1.3.1');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(2);
        $this->waitForVisible("xpath=(//*[contains(text(),'Господи, да ведь там в вашем бюджете')])");
        $this->stop();
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
        $this->optimal_click('link=F32');
        sleep(5);
        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки')])");
        sleep(10);

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->run_event('ET1.3.1');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(2);
        $this->waitForVisible("xpath=(//*[contains(text(),'Господи, да ведь там в вашем бюджете')])");
        $this->stop();
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
        $this->optimal_click('link=F32');
        sleep(5);
        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл')])");
        sleep(10);

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->run_event('ET1.3.2');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(2);
        $this->waitForVisible("xpath=(//*[contains(text(),'Господи, и что же мне теперь делать')])");
        $this->stop();
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
        $this->optimal_click('link=F32');
        sleep(5);
        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Ты не мог бы мне помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки')])");
        sleep(10);

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->run_event('ET1.3.2');

        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(2);
        $this->waitForVisible("xpath=(//*[contains(text(),'Господи, и что же мне теперь делать')])");
        $this->stop();
    }
}