<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на тестирование флага F13 (для SK1415)
 */
class F13_SK1415_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * testSK1415_Case1() тестирует задачу SKILIKS-1415
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Я тебе сейчас перешлю файл, ты посмотри"
     * 3. Проверяем, что флаг F4 включен (F4=1)
     * 4. Проверяем, что флаг F13 выключен (F13=0)
     * 5. Запускаем ET1.3.1
     * 6. На звонящий телефон отвечаем
     * 7. Кликаем по диалогу до фразы "Ну, хорошо, забудь. Занимайся своими делами."
     * 8. Проверяем, что F13 включен (F13=1)
     */
    public function testSK1415_Case1()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.3');
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),' Сергей, привет! Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл, ты посмотри')])");

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->assertTrue($this->verify_flag('F13','0'));

        $this->run_event('ET1.3.1');

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            $this->optimal_click("css=li.icon-active.phone a");
            $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
            $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

            $this->optimal_click("xpath=(//*[contains(text(),'Да, я тебе все переслал.')])");
            $this->optimal_click("xpath=(//*[contains(text(),'Ну, хорошо, забудь. Занимайся своими делами.')])");

            $this->assertTrue($this->verify_flag('F13','1'));
        }
        else
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");

    }


    /**
     * testSK1415_Case2() тестирует задачу SKILIKS-1415
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Я тебе сейчас перешлю файл, ты посмотри"
     * 3. Проверяем, что флаг F4 включен (F4=1)
     * 4. Проверяем, что флаг F13 выключен (F13=0)
     * 5. Запускаем ET1.3.2
     * 6. На звонящий телефон отвечаем
     * 7. Кликаем по диалогу до фразы "Да уж … Хорошо, я сам все сделаю"
     * 8. Проверяем, что F13 включен (F13=1)
     */
    public function testSK1415_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),' Сергей, привет! Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл, ты посмотри')])");

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->assertTrue($this->verify_flag('F13','0'));

        $this->run_event('ET1.3.2');

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            $this->optimal_click("css=li.icon-active.phone a");
            $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
            $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

            $this->optimal_click("xpath=(//*[contains(text(),'Какая экономика?! Сплошная арифметика.')])");
            $this->optimal_click("xpath=(//*[contains(text(),'Да уж … Хорошо, я сам все сделаю')])");

            $this->assertTrue($this->verify_flag('F13','1'));
        }
        else
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");

    }

    /**
     * testSK1415_Case3() тестирует задачу SKILIKS-1415
     *
     * 1. Запускаем E1.3
     * 2. Кликаем по диалогу до фразы "Я тебе сейчас перешлю файл, ты посмотри"
     * 3. Проверяем, что флаг F4 включен (F4=1)
     * 4. Проверяем, что флаг F13 выключен (F13=0)
     * 5. Запускаем E1.3.3
     * 6. На звонящий телефон отвечаем
     * 7. Кликаем по диалогу до фразы "Понятно. Сделаю все сам. Логисты, кажется"
     * 8. Проверяем, что F13 включен (F13=1)
     */
    public function testSK1415_Case3()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->run_event('E1.3');

        $this->optimal_click("xpath=(//*[contains(text(),' Сергей, привет! Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебе сейчас перешлю файл, ты посмотри')])");

        $this->assertTrue($this->verify_flag('F4','1'));

        $this->assertTrue($this->verify_flag('F13','0'));

        $this->run_event('E1.3.3');

        if ($this->is_it_done("css=li.icon-active.phone a"))
        {
            $this->optimal_click("css=li.icon-active.phone a");
            $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
            $this->click(Yii::app()->params['test_mappings']['phone']['reply']);

            $this->optimal_click("xpath=(//*[contains(text(),'Как твои дела?')])");
            $this->optimal_click("xpath=(//*[contains(text(),'Вообще-то я про сводный бюджет.')])");
            $this->optimal_click("xpath=(//*[contains(text(),'Да, отличная методика, я сам ее и составлял.')])");
            $this->optimal_click("xpath=(//*[contains(text(),'Понятно. Сделаю все сам. Логисты, кажется,')])");

            $this->assertTrue($this->verify_flag('F13','1'));
        }
        else
        {
            print ("The test crashed! This action couldn't be active in such situation!");
        }

        $this->click("css=input.btn.btn-simulation-stop");

    }
}