<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на тестирование флага F14 (для SK1427)
 */
class F14_SK1427_P_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

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
        // tests to checking the actions before F14 = 1
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])",'-');

        //$this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше')])");

        $this->assertTrue($this->verify_flag('F14','1'));

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "15");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "30");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        // downloading all messages
        sleep(20);
        $this->optimal_click("css=li.icon-active.mail a");
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("Презентация для ГД_итог"));

    }

    /**
     * testSK1427_Case2() тестирует задачу SKILIKS-1427 для статуса DIALOG
     *
     * 1. Проверяем, что флаг F14 = 0
     * 2. Запускаем E2.4
     * 3. Кликаем по диалогу до фразы "Раз ты уверена, что задача простая и времени"
     * 4. Проверяем, что флаг F14 изменился (F14 = 1)
     * 5. Запускаем E12
     * 6. Ожидаем появления фразы "Ваша встреча переносится с 16.00 на 18.00."
     */
    public function testSK1427_Case2() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])",'-');

        //$this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай мы все-таки посмотрим, что у')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Пересылаю. Но смысл-то какой? Это')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Послушай, так здесь и смотреть нечего!')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Но ведь я сказала, что успею! Обычная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Раз ты уверена, что задача простая и времени')])");


        $this->assertTrue($this->verify_flag('F14','1'));

        // tests to checking the actions after F14 = 1 (DIALOG)
        $this->run_event('E12',"xpath=(//*[contains(text(),'Ваша встреча переносится с 16.00 на 18.00.')])",'-');

        //$this->waitForVisible("xpath=(//*[contains(text(),'Ваша встреча переносится с 16.00 на 18.00.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Как же так! Он же сам настаивал!')])");

    }

    /**
     * testSK1427_Case3_1() тестирует задачу SKILIKS-1427 для статуса REPLICA
     *
     * 1. Проверяем, что флаг F14 = 0
     * 2. Запускаем E2.4
     * 3. Кликаем по диалогу до фразы "Ладно, раз ты уверена, что справишься"
     * 4. Проверяем, что флаг F14 изменился (F14 = 1)
     * 5. Устанавливаем время на 15:50 так как ожидаемое событие ET12.1 должно в это время запуститься
     * 6. Проверяем, что событие ET12.1 запускается и звонит телефон нажимаем "Не ответить"
     * 7. Проверяем, что через 10 минут звонок ET12.3 (для этого перематываем время на 10 минут вперед)
     * 8. Ожидаем, что появилась фраза "Валерий Семенович сказал, что презентация"
     */
    public function testSK1427_Case3_1() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])",'-');

        //$this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай мы все-таки посмотрим, что у')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Пересылаю. Но смысл-то какой? Это')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что такое?! Так ведь она пустая!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хотелось бы знать')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Если бы вы не отвлекали меня по')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно, раз ты уверена, что справишься')])");


        $this->assertTrue($this->verify_flag('F14','1'));

        // tests to checking the actions after F14 = 1 (REPLICA)

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "15");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "50");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        // в 15:50 запускается ET12.1
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        // через 10 минут звонок ET12.3
        $this->transfer_time(10);

        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Ваша встреча переносится с 16.00 на 18.00.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Как же так! Он же сам настаивал!')])");
    }

    /**
     * testSK1427_Case3_2() тестирует задачу SKILIKS-1427 для статуса REPLICA
     *
     * 1. Проверяем, что флаг F14 = 0
     * 2. Запускаем E2.4
     * 3. Кликаем по диалогу до фразы "Ладно, раз ты уверена, что справишься"
     * 4. Проверяем, что флаг F14 изменился (F14 = 1)
     * 5. Устанавливаем время на 15:50 так как ожидаемое событие ET12.1 должно в это время запуститься
     * 6. Проверяем, что событие ET12.1 запускается и звонит телефон нажимаем "Ответить"
     * 7. Проверяем, что появилась фраза "Валерий Семенович просит прямо сейчас"
     * 7. Проверяем, что через 10 минут звонит ET12.3 (для этого перематываем время на 10 минут вперед) и запускается диалог E2
     * 8. Ожидаем, что появилась фраза "Валерий Семенович сказал, что презентация"
     */
    public function testSK1427_Case3_2() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])",'-');

        //$this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай мы все-таки посмотрим, что у')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Пересылаю. Но смысл-то какой? Это')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что такое?! Так ведь она пустая!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хотелось бы знать')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Если бы вы не отвлекали меня по')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно, раз ты уверена, что справишься')])");


        $this->assertTrue($this->verify_flag('F14','1'));

        // tests to checking the actions after F14 = 1 (REPLICA)
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "15");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "50");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        // в 15:50 запускается ET12.1
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);

        // запускается ET12.2
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас перешлю.')])");

        // через 10 минут звонок ET12.3
        $this->transfer_time(10);

        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Ваша встреча переносится с 16.00 на 18.00.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Как же так! Он же сам настаивал!')])");
    }

    /**
     * testSK1427_Case3_3() тестирует задачу SKILIKS-1427 для статуса REPLICA
     *
     * 1. Проверяем, что флаг F14 = 0
     * 2. Запускаем E2.4
     * 3. Кликаем по диалогу до фразы "Ладно, раз ты уверена, что справишься"
     * 4. Проверяем, что флаг F14 изменился (F14 = 1)
     * 5. Устанавливаем время на 15:50 так как ожидаемое событие ET12.1 должно в это время запуститься
     * 6. Проверяем, что событие ET12.1 запускается и звонит телефон нажимаем "Ответить"
     * 7. Проверяем, что появилась фраза "Валерий Семенович просит прямо сейчас"
     * 7. Проверяем, что через 10 минут звонит телефон (для этого перематываем время на 10 минут вперед) и запускается диалог E2
     * 8. Ожидаем, что появилась фраза "Валерий Семенович сказал, что презентация"
     */
    public function testSK1427_Case3_3() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])",'-');

        //$this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай мы все-таки посмотрим, что у')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Пересылаю. Но смысл-то какой? Это')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что такое?! Так ведь она пустая!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хотелось бы знать')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Если бы вы не отвлекали меня по')])");
        $this->optimal_click("xpath=(//*[contains(text(),'У меня нет уверенности, что ты сделаешь')])");


        $this->assertTrue($this->verify_flag('F14','0'));

        // tests to checking the actions after F14 = 1 (REPLICA)
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "15");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "50");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        // в 15:50 запускается ET12.1
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

    }
}