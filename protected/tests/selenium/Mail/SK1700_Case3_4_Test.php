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
     * testSK1273_Case3() по задаче SKILIKS-1273
     *
     * тест на отправку сообщений со сложными префиксами для правильных писем (тех, которые есть в сценарие)
     * (тест того, что мы можем ответить для любого сообщения)
     */
    public function testSK1700_Case3() {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
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


    /**
     * testSK1273_Case4() по задаче SKILIKS-1273
     *
     * тест на отправку сообщений со сложными префиксами для правильных писем (тех, которые есть в сценарие) :
     * re, rere, rerere, rererere, fwdrerere (MS30, M31, MS32, M33)
     */
    public function testSK1273_Case4() {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "14");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "06");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->assertTrue($this->incoming_counter(13));

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);

        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->mail_comes("срочно! Требования клиентов"));

        $this->mail_open("срочно! Требования клиентов");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['reply_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['reply_email']);

        $this->waitForTextPresent("Re: срочно! Требования клиентов");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Re: срочно! Требования клиентов"));

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['inbox']);

        $this->mail_open("срочно! Требования клиентов");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['reply_all_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['reply_all_email']);

        $this->waitForTextPresent("Re: срочно! Требования клиентов");

        $this->assertTextPresent("Скоробей");
        $this->assertTextPresent("Бобр");
        $this->assertTextPresent("Денежная");
        $this->assertTextPresent("Трудякин");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");



        $this->click("css=input.btn.btn-simulation-stop");
    }
}