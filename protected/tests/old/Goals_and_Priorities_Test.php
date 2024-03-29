<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Следование приоритетам (Область обучения №1)
 */
class Goals_and_Priorities_Test extends SeleniumTestHelper
{
    public function testGoalsAndPriorities_Positive()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("testGoalsAndPriorities_Positive", 1);

        $this->clearEventQueueBeforeEleven('RST1');

        $this->run_event('E3',"xpath=(//*[contains(text(),'Через двадцать минут? Тогда времени')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),' Спасибо тебе, значит, через две')])");
        sleep(2);
        $this->run_event('RST7',Yii::app()->params['test_mappings']['icons_active']['phone'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Люба, у тебя что-то срочное?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Прости, пожалуйста, но сейчас никак не могу!')])");
        sleep(2);
        $this->run_event('RST10',Yii::app()->params['test_mappings']['icons_active']['phone'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Привет, Петр. У тебя что-то срочное?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, понял тебя, спасибо! Значит, с работы выходить')])");
        sleep(2);
        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения. Впредь такого не будет.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, я готовлю презентацию для Босса, могу я ')])");
        sleep(20);

        $this->optimal_click('link=F14');
        $this->optimal_click('link=F36');
        $this->assertTrue($this->verify_flag('F14','1'));

        $this->run_event('E12',"xpath=(//*[contains(text(),'Я вас очень прошу, найдите сегодня любое время')])",'click');
        sleep(5);

        $this->run_event('ET8',Yii::app()->params['test_mappings']['icons_active']['door'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);
        $this->optimal_click("xpath=(//*[contains(text(),'Привет, Семен! С бюджетом покончено')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мы в двадцать минут впишемся?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, пойдем, но у меня только двадцать минут.')])");
        sleep(2);
        $this->simulation_showLogs();
        sleep(5);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['group_1_1']);
        $this->assertText(Yii::app()->params['test_mappings']['log']['group_1_1'],"100.00");
    }


    public function testGoalsAndPriorities_Negative()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("testGoalsAndPriorities_Negative", 1);

        $this->run_event('E3',"xpath=(//*[contains(text(),'Через двадцать минут? Тогда времени на разговор ')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Вот спасибо! Выручил! Давай до вечера, часов в')])");
        sleep(2);
        $this->run_event('ET8',Yii::app()->params['test_mappings']['icons_active']['door'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['deny']);
        sleep(2);
        $this->clearEventQueueBeforeEleven('RST1');
        sleep(2);
        $this->clearEventQueueBeforeEleven('RST7');
        sleep(2);
        $this->run_event('T2',"xpath=(//*[contains(text(),'Иван, доброе утро! Как дела? Ты про нашу встречу')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давай встретимся вечером, после 18.00 ')])");
        sleep(2);
        $this->run_event('RS2',"xpath=(//*[contains(text(),'Приветствую, Егор!  У тебя что-то срочное?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Егор, я сегодня встречаюсь с первым клиентом!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, но я могу только вечером, после шести! ')])");
        sleep(2);
        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Слушаюсь, Раиса Романовна, сейчас сделаю.')])");
        sleep(2);
        $this->optimal_click('link=F14');
        $this->optimal_click('link=F36');
        $this->assertTrue($this->verify_flag('F14','1'));

        $this->run_event('E12',"xpath=(//*[contains(text(),'Хорошо, сейчас внесу в план новое время.')])",'click');
        sleep(5);

        $this->run_event('E15',"xpath=(//*[contains(text(),'Это то, что нам надо, Раиса Романовна!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Раз пять минимум, я уже не говорю про продажи.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'К какому времени я должен подготовить подборку разных программ?')])");
        sleep(20);

        $this->run_event('E3.1',"xpath=(//*[contains(text(),'Добрый день! Да, слышал. Важный проект!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Благодарю, польщен. Так о чем вы хотели поговорить?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Сколько именно времени  вам нужно и для чего?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да я не отказываюсь с вами встречаться, хочу')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я вас понял. Давайте сегодня после 18.00! ')])");
        sleep(5);
        $this->simulation_showLogs();
        sleep(5);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['group_1_1']);
        $this->assertText(Yii::app()->params['test_mappings']['log']['group_1_1'],"0.00");
    }

}