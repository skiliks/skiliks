<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 3/4/13
 * Time: 1:26 PM
 * To change this template use File | Settings | File Templates.
 */
class F14_SK1427_P_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    // tests to checking the actions for status MAIL
    public function testSK1427_Case1()
    {
        // tests to checking the actions before F14 = 1
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4');

        $this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
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

    // tests to checking the actions for status DIALOG
    public function testSK1427_Case2() {

        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4');

        $this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай мы все-таки посмотрим, что у')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Пересылаю. Но смысл-то какой? Это')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Послушай, так здесь и смотреть нечего!')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Но ведь я сказала, что успею! Обычная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Раз ты уверена, что задача простая и времени')])");


        $this->assertTrue($this->verify_flag('F14','1'));

        // tests to checking the actions after F14 = 1 (DIALOG)

        $this->run_event('E12');

        $this->waitForVisible("xpath=(//*[contains(text(),'Ваша встреча переносится с 16.00 на 18.00.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Как же так! Он же сам настаивал!')])");

    }

    // tests to checking the actions for status REPLICA
    public function testSK1427_Case3_1() {

        $this->markTestIncomplete();
        $this->start_simulation();

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4');

        $this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
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
        // здесь сейчас вылетает, т.к. не работает телефон (одна кнопка "не ответить")
        $this->optimal_click("css=li.icon-active.phone a");
        $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $this->click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        // через 10 минут звонок ET12.3
        $this->transfer_time(10);

        // не знаю, чи треба "відповісти" натискати чи ні, бо неадекватно працює зараз телефон...

        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович сказал, что презентация')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Как же так! Он же сам настаивал!')])");
    }

    public function testSK1427_Case3_2() {

        $this->markTestIncomplete();
        $this->start_simulation();

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4');

        $this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
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
        // здесь сейчас вылетает, т.к. не работает телефон (одна кнопка "не ответить")
        $this->optimal_click("css=li.icon-active.phone a");
        $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

        // запускается ET12.2

        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас перешлю.')])");

        // через 10 минут звонок ET12.3
        $this->transfer_time(10);

        // запускается E2
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович сказал, что презентация')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Как же так! Он же сам настаивал!')])");
    }

    public function testSK1427_Case3_3() {

        $this->markTestIncomplete();
        $this->start_simulation();

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]","0");

        $this->run_event('E2.4');

        $this->waitForVisible("xpath=(//*[contains(text(),'Марина Крутько, добрый день.')])");
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
        $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->click(Yii::app()->params['test_mappings']['phone']['reply']);


        // запускается ET12.2

        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да в прошлом году')])");

        $this->transfer_time(10);

        // запускается E2
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович сказал, что презентация')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Как же так! Он же сам настаивал!')])");
    }
}