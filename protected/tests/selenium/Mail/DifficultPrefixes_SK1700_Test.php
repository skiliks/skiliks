<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на отправку сообщений со сложными префиксами re, rere, rerere, rererere, fwdrerere (для SK1700)
 */
class DifficultPrefixes_SK1700_Test extends SeleniumTestHelper
{
    /**
     * testSK1273_Case3() по задаче SKILIKS-1273
     *
     * тест на отправку сообщений со сложными префиксами для правильных писем (тех, которые есть в сценарие)
     * (тест того, что мы можем ответить для любого сообщения)
     */
    public function test_DifficultPrefixes_SK1700_Case3() {
        //$this->markTestIncomplete('Some delay needs');
        $this->start_simulation("test_DifficultPrefixes_SK1700_Case3");
        sleep(5);
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "11");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);

        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        sleep(10);
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'консультанты и новый проект')])"));

        $this->optimal_click("xpath=(//*[contains(text(),'консультанты и новый проект')])");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['reply_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['reply_email']);

        $this->waitForTextPresent("Re: консультанты и новый проект");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        sleep(5);
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Re: консультанты и новый проект')])"));
        $this->simulation_stop();
    }


    /**
     * testSK1273_Case4() по задаче SKILIKS-1273
     *
     * тест на отправку сообщений со сложными префиксами для правильных писем
     * (проверка правильности работы с "коллективными" письмами)
     *
     */
    public function test_DifficultPrefixes_SK1700_Case4() {
        //$this->markTestIncomplete();
        $this->start_simulation("test_DifficultPrefixes_SK1700_Case4");
        sleep(5);
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "14");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "06");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->assertTrue($this->incoming_counter(13));

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);

        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->assertTrue($this->isTextPresent("срочно! Требования клиентов"));

        $this->optimal_click("xpath=(//*[contains(text(),'срочно! Требования клиентов')])");

        $this->mouseOver(Yii::app()->params['test_mappings']['mail_main']['reply_email']);
        $this->click(Yii::app()->params['test_mappings']['mail_main']['reply_email']);

        $this->waitForTextPresent("Re: срочно! Требования клиентов");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'новое письмо')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->isTextPresent("Re: срочно! Требования клиентов"));

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['inbox']);

        $this->optimal_click("xpath=(//*[contains(text(),'срочно! Требования клиентов')])");

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
        $this->simulation_stop();
    }
}