<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест на диалоги, Case 6.
 * Пошагово запускаем диалоги из /Users/Tony/Dropbox/projectx - development/1. Documentation/1.1 Scenario/1.1.1 Оценка/Тесты/Расчет оценки_тест3_final.xls
 * После чего в Simulation points сверяем суммы оценок поведений positive, negative & personal по mail matrix и all dialogs
 */
class Case6_SK1792_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1792()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('E1.3.2',"xpath=(//*[contains(text(),'Я тебя для чего тут держу?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, я сам все сделаю, письмо от логистов у меня тоже есть')])");

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('E2.7',"xpath=(//*[contains(text(),'Вот уж не ждал от тебя такого легкомыслия!')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Потрясающая безответственность!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Столько агрессии…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тогда уж и я скажу все, что думаю')])");



        $this->run_event('E13',"xpath=(//*[contains(text(),'я на совещание опаздываю')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Кхе….кхе…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уважаю твое решение')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну вот видишь…')])");

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'сейчас поговорю с ним и уточню задание')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        //$this->optimal_click(Yii::app()->params['test_mappings']['dev']['sim_points']);
        $this->waitForTextPresent('Simulation points');
        $this->checkSimPoints('11.667','-10');
        $this->checkLearningArea('2.56','0.00','0.00','4.55','2.18','15','8.33','10');
    }
}
