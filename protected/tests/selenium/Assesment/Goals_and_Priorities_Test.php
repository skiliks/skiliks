<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Следование приоритетам (Область обучения №1)
 */
class Goals_and_Priorities_Test extends SeleniumTestHelper {
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testGoalsAndPriorities_Positive()
    {
        $this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('E3',"xpath=(//*[contains(text(),'Через двадцать минут? Тогда времени')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),' Спасибо тебе, значит, через две')])");

        $this->run_event('RST7',"css=li.icon-active.phone a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Люба, у тебя что-то срочное?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Прости, пожалуйста, но сейчас никак не могу!')])");

        $this->run_event('RST10',"css=li.icon-active.phone a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Привет, Петр. У тебя что-то срочное?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, понял тебя, спасибо! Значит, с работы выходить')])");

        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения. Впредь такого не будет.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, я готовлю презентацию для Босса, могу я ')])");
        sleep(20);

        $this->run_event('ET8',"css=li.icon-active.door a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);
        $this->optimal_click("xpath=(//*[contains(text(),'Привет, Семен! С бюджетом покончено')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мы в двадцать минут впишемся?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, пойдем, но у меня только двадцать минут.')])");

        $this->optimal_click("xpath=//div/div[4]/form[1]/fieldset/table[1]/thead/tr/th[6]/a");
        $this->assertTrue($this->verify_flag('F14','1'));

        $this->run_event('E12',"xpath=(//*[contains(text(),'Я вас очень прошу, найдите сегодня любое время')])",'click');
        sleep(5);
        //$this->run_event('Е15',"xpath=(//*[contains(text(),'Раиса Романовна, прошу прощения, но я планирую ')])",'click');
        ///sleep(10);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        sleep(20);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['goals'],"100");
        $this->assertText(Yii::app()->params['test_mappings']['log']['goals'],"100");
    }


    public function testGoalsAndPriorities_Negative()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E3',"xpath=(//*[contains(text(),'Через двадцать минут? Тогда времени на разговор ')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Вот спасибо! Выручил! Давай до вечера, часов в')])");

        $this->run_event('ET8',"css=li.icon-active.door a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['deny']);

        $this->run_event('RST7',"css=li.icon-active.phone a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->run_event('T2',"xpath=(//*[contains(text(),'Иван, доброе утро! Как дела? Ты про нашу встречу')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давай встретимся вечером, после 18.00 ')])");

        $this->run_event('RS2',"xpath=(//*[contains(text(),'Приветствую, Егор!  У тебя что-то срочное?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Егор, я сегодня встречаюсь с первым клиентом!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, но я могу только вечером, после шести! ')])");

        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Слушаюсь, Раиса Романовна, сейчас сделаю.')])");

        $this->optimal_click("xpath=//div/div[4]/form[1]/fieldset/table[1]/thead/tr/th[6]/a");
        $this->assertTrue($this->verify_flag('F14','1'));

        $this->run_event('E12',"xpath=(//*[contains(text(),'Хорошо, сейчас внесу в план новое время.')])",'click');

        $this->run_event('E15',"xpath=(//*[contains(text(),'Это то, что нам надо, Раиса Романовна!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Раз пять минимум, я уже не говорю про продажи.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'К какому времени я должен подготовить подборку разных программ?')])");
        sleep(20);

        $this->run_event('E3.1',"xpath=(//*[contains(text(),'Добрый день! Да, слышал. Важный проект!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Благодарю, польщен. Так о чем вы хотели поговорить?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Сколько именно времени  вам нужно и для чего?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да я не отказываюсь с вами встречаться, хочу')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я вас понял. Давайте сегодня после 18.00! ')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        sleep(60);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['goals']);
        $this->assertText("xpath=//div[1]/div/div[2]/table[17]/tbody/tr[1]/td[4]","0.00");
        $this->assertText("xpath=//div[1]/div/div[2]/table[17]/tbody/tr[2]/td[4]","0.00");
    }

}