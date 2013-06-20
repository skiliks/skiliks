<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на отправку сообщений со сложными префиксами re, rere, rerere, rererere, fwdrerere (для SK1273)
 */
class DifficultPrefixes_SK1273_Test extends SeleniumTestHelper
{

    /**
     * testSK1273_Case1() по задаче SKILIKS-1273
     *
     * тест на отправку сообщений со сложными префиксами для правильных писем (тех, которые есть в сценарие) :
     * re, rere, rerere, rererere, fwdrerere (MS30, M31, MS32, M33)
     */
    public function test_DifficultPrefixes_SK1273_Case1() {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->write_email();
        $this->addRecipient("xpath=(//*[contains(text(),'Трудякин')])");
        //тема
        $this->addTheme("xpath=(//*[contains(text(),'Срочно жду бюджет логистики')])");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        $this->optimal_click(Yii::app()->params['test_mappings']['icon']['close']);

        $this->optimal_click("css=li.icon-active.mail a");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Срочно жду бюджет логистики"));

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['inbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("Re: Срочно жду бюджет логистики"));

        $this->mail_open("Re: Срочно жду бюджет логистики");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['reply_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['reply_email']);

        $this->waitForTextPresent("Re: Re: Срочно жду бюджет логистики");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Re: Re: Срочно жду бюджет логистики"));
        $this->optimal_click(Yii::app()->params['test_mappings']['icon']['close']);

        $this->optimal_click("css=li.icon-active.mail a");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['inbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("Re: Re: Re: Срочно жду бюджет логистики"));

        $this->mail_open("Re: Re: Re: Срочно жду бюджет логистики");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['reply_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['reply_email']);

        $this->waitForTextPresent("Re: Re: Re: Re: Срочно жду бюджет логистики");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Re: Re: Re: Re: Срочно жду бюджет логистики"));
        $this->optimal_click(Yii::app()->params['test_mappings']['icon']['close']);

        $this->assertFalse($this->isElementPresent("css=li.icon-active.mail a"));

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['inbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("Re: Re: Re: Срочно жду бюджет логистики"));

        $this->mail_open("Re: Re: Re: Срочно жду бюджет логистики");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['forward_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['forward_email']);

        $this->waitForTextPresent("Fwd: Re: Re: Re: Срочно жду бюджет логистики");

        $this->addRecipient("xpath=(//*[contains(text(),'Крутько')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Fwd: Re: Re: Re: Срочно жду бюджет логистики"));
        $this->close();
    }


    /**
     * testSK1273_Case2() по задаче SKILIKS-1273
     *
     * тест на отправку сообщений со сложными префиксами для писем, которых нет в сценарие
     * (тест на то, что любое письмо можно кому-угодно переслать)
     */
    public function test_DifficultPrefixes_SK1273_Case2() {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(30);
        $this->run_event('M65', "css=li.icon-active.mail a", 'click');

        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("вакцинация!"));

        $this->mail_open("вакцинация!");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['forward_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['forward_email']);

        $this->waitForTextPresent("Fwd: вакцинация!");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Трудякин')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Трудякин')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Трудякин')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Fwd: вакцинация!"));
        $this->close();
    }
}