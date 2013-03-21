<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на тестирование флага F4 (для SK1700)
 */
class SK1700_Case3_4_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * testSK1413_N_Case1() тестирует задачу SKILIKS-1700
     *
     * 1.
     */
    public function testSK1700_Case3() {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(1);
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "11");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        sleep(2);
        $this->assertTrue($this->incoming_counter(2));
        $this->optimal_click("css=li.icon-active.mail a");

        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("консультанты и новый проект"));

        $this->mail_open("консультанты и новый проект");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['reply_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['reply_email']);

        $this->waitForTextPresent("Re: консультанты и новый проект");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Re: консультанты и новый проект"));

        $this->click("css=input.btn.btn-simulation-stop");
    }



    public function testSK1273_Case4() {
        $this->markTestIncomplete();
        $this->start_simulation();

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "17");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "23");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->assertTrue($this->incoming_counter(27));

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);

        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("вакцинация!"));

        $this->mail_open("вакцинация!");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['forward_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['forward_email']);

        $this->waitForTextPresent("Fwd: вакцинация!");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Fwd: вакцинация!"));

        $this->click("css=input.btn.btn-simulation-stop");
    }
}