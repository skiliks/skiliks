<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты (негативные) на тестирование флага F4 (для SK1413)
 */
class SK1273_Case1_2_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * testSK1413_N_Case1() тестирует задачу SKILIKS-1413
     *
     * 1.
     */
    public function testSK1273_Case1() {
        // $this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->run_event('MS30');
        $this->assertTrue($this->incoming_counter(1));
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

        $this->click("css=.btn-close button");

        $this->assertTrue($this->incoming_counter(1));
        $this->optimal_click("css=li.icon-active.mail a");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['inbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Re: Re: Re: Срочно жду бюджет логистики"));

        $this->mail_open("Re: Срочно жду бюджет логистики");

        $this->click("css=input.btn.btn-simulation-stop");
    }
}