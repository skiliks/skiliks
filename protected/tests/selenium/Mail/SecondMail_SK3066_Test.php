<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на проверку блокировки тем в почте, если правильная MS с такой темой была отправлена и не блокировки тем по гибким коммуникациям (для SK3066)
 */
class SecondMail_SK3066_Test extends SeleniumTestHelper
{
    public function test_SecondMail_SK3066()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("SecondMail_SK3066_Test");
        $this->write_email();
        // Пишем письмо с темой правильной MS
        $this->addRecipient(Yii::app()->params['test_mappings']['mail_contacts']['denejnaya']);
        $this->addTheme("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        sleep(2);
        // Пишем новое письмо на тему гибких коммуникаций и проверяем, что темы правильной MS нет
        $this->write_email();
        $this->addRecipient(Yii::app()->params['test_mappings']['mail_contacts']['denejnaya']);
        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->assertFalse($this->isVisible("xpath=(//*[contains(text(),'Сводный бюджет')])"));
        $this->addTheme("xpath=(//*[contains(text(),'Новая тема')])");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        //Проверяем, что тема гибких коммуникаций осталась
        $this->write_email();
        $this->addRecipient(Yii::app()->params['test_mappings']['mail_contacts']['denejnaya']);
        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Новая тема')])"));

        $this->simulation_stop();
    }
}