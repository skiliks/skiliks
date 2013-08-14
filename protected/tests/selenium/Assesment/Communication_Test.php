<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Оптимальный выбор каналов коммуникации (Область обучения №5)
 */
class Communication_Test extends SeleniumTestHelper
{

    public function test_communication()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->optimal_click('link=F41');

        $this->run_event('T2',"xpath=(//*[contains(text(),'Иван, привет! Это Федоров')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давай встретимся вечером')])");
        sleep(5);

        $this->run_event('T3.1',"xpath=(//*[contains(text(),'Егор, приветствую')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, но у тебя не больше пяти минут')])");
        sleep(5);

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'сейчас поговорю с ним и уточню задание')])");
        sleep(5);

        $this->run_event('RS8.1',"xpath=(//*[contains(text(),'Добрый день! Федоров. У меня есть к вам важный вопрос по теме бюджета')])", 'click');
        sleep(7);

        $this->run_event('MS20');
        sleep(5);
        $this->run_event('MS51');
        sleep(5);
        $this->run_event('MS40');
        sleep(5);
        $this->run_event('MS52');
        sleep(5);

        $this->simulation_showLogs();
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['group_3_1'],"100.00");
        $this->assertText(Yii::app()->params['test_mappings']['log']['group_3_1'],"100.00");
    }
}
