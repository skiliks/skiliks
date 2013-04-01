<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест на диалоги, Case 4.
 * Пошагово запускаем диалоги из /Users/Tony/Dropbox/projectx - development/1. Documentation/1.1 Scenario/1.1.1 Оценка/Тесты/Расчет оценки_тест3_final.xls
 * После чего в Simulation points сверяем суммы оценок поведений positive, negative & personal по mail matrix и all dialogs
 */
class Case4_SK1790_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1790()
    {
        $this->start_simulation();
        sleep(2);
        $this->run_event('E1',"xpath=(//*[contains(text(),'Раиса Романовна, помню про бюджет')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну, с помощью Крутько я должен управиться в эти сроки')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, бросай все свои дела')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Закончила? Теперь слушай сюда')])");
        //отправка MS21
        sleep(10);

        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Сергей, нужно сделать бюджет. Срочно.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В чем  именно ты не уверен?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уже сказал, что дело срочное и что ты мне нужен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки придется выполнить это задание')])");
        //отправка MS22
        sleep(10);

        $this->assertTrue($this->verify_flag('F3','1'));

        $this->run_event('E2.2',"xpath=(//*[contains(text(),'Босс звонил. Требует эту презентацию.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),' Ах да. Помню. Шли, что есть вместе с твоими мыслями и прошлогодней презентацией')])");

        $this->run_event('E8.3',"xpath=(//*[contains(text(),'Конечно читал. Хорошее письмо, обстоятельное')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Понял тебя,  скажу своему аналитику, чтобы срочно служебку писал')])");

        $this->run_event('E8.5',"xpath=(//*[contains(text(),'Сергей, удобно тебе говорить?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну ладно, набери меня, когда освободишься')])");

        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения. Впредь такого не будет')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, я готовлю презентацию для Босса')])");
        sleep(10);
        $this->run_event('MS68');
        sleep(2);
        $this->run_event('MS70');
        sleep(2);
        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Но я с понедельника в отпуске')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В понедельник, скажем в 10.00, будет моя сотрудница Марина Крутько')])");

        $this->run_event('E12.5',"xpath=(//*[contains(text(),'Но мы ведь уже договорились, и я успел поменять мой график')])", 'click');

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('RS1',"xpath=(//*[contains(text(),'Кто вам нужен?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, а вы кто?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Слушайте, у меня времени нет, я смогу только в обед!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас от меня придет человек.')])");
        sleep(2);
        $this->optimal_click("xpath=(//*[contains(text(),'Доброе утро, Сергей! Нужна твоя помощь!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понимаю. Много времени моя просьба не займет.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это так. Но речь идет всего о пятнадцати минутах.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, заскочи, только быстро!')])");
        sleep(2);
        $this->run_event('RS2',"xpath=(//*[contains(text(),'Доброе утро, Егор. Не совсем – я бюджетом занят.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Какой план? Я бюджетом занят!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я могу тебе предложить достойную альтернативу – повидайся с моим лучшим аналитиком Мариной Крутько')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сегодня вечером, после шести! ')])");
        sleep(2);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['sim_points']);

        //оценка не совпадает
        sleep(10);

        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_positive'],"3.292");
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_negative'],"-32");
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_personal'],"4.227");
    }
}
