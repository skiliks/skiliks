<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Оптимальный выбор каналов коммуникации (Область обучения №5)
 */
class Communication_Choice_Test extends SeleniumTestHelper
{

    public function test_communication_choice()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('T2',"xpath=(//*[contains(text(),'Иван, привет! Это Федоров')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давай встретимся вечером')])");

        $this->run_event('T3.1',"xpath=(//*[contains(text(),'Егор, приветствую')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, но у тебя не больше пяти минут')])");


        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'сейчас поговорю с ним и уточню задание')])");

        $this->run_event('RS6', "xpath=(//*[contains(text(),'давайте я вам перешлю этот показатель')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Через пять минут данные будут у вас')])");
        sleep(5);

        $this->run_event('RS8.1',"xpath=(//*[contains(text(),'Добрый день! Федоров. У меня есть к вам важный вопрос по теме бюджета')])", 'click');
        sleep(7);

        $this->run_event('MS20');
        sleep(2);
        $this->run_event('MS51');
        sleep(2);
        $this->run_event('MS40');
        sleep(2);
        $this->run_event('MS52');
        sleep(2);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['communication4'],"100");
        $this->assertText(Yii::app()->params['test_mappings']['log']['communication4'],"100");
        $this->stop();
    }
}
