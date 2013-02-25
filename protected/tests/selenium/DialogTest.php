<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 13.02.13
 * Time: 16:17
 * To change this template use File | Settings | File Templates.
 */
class SelDialogTest extends CWebTestCase
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testE2_4_2_9() {

        $this->deleteAllVisibleCookies();
        $this->open('/site/');
        $this->setSpeed("1000");
        $this->waitForVisible('id=login');
        $this->type("id=login", "asd");
        $this->type("id=pass", "123");
        $this->click("css=input.btn.btn-primary");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=//input[@value='Начать симуляцию developer']")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->click("xpath=//input[@value='Начать симуляцию developer']");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("id=addTriggerSelect")) break;
            } catch (Exception $e) {}
            sleep(1);
        }
        $this->type("id=addTriggerSelect", "E1.2");
        $this->click("css=input.btn.btn-primary");
        sleep(15);
        $this->click("link=— Марина, есть срочная работа.");
        sleep(15);
        $this->click("link=exact:— Закончила? Теперь слушай сюда. Если мы не сдадим бюджет Денежной через два часа, она нас обоих уволит. Пересылаю тебе файл, приступай немедленно.");
        sleep(15);

        // send message MS21
        $this->markTestIncomplete();
        $this->click("id=icons_email");
        sleep(15);
        $this->click("link=новое письмо");
        sleep(15);
        $this->click("id=MailClient_RecipientsList");
        sleep(15);

        $this->waitForVisible("xpath=(//*[contains(text(),'Крутько')])");
        sleep(15);
        $this->mouseOver("xpath=(//*[contains(text(),'Крутько')])");
        sleep(15);
        $this->click("xpath=(//*[contains(text(),'Крутько')])");
        sleep(5);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        sleep(5);
        $this->click("//div[@class='mail-popup']//td[1]/div['Продолжить']");
        sleep(5);
        $this->select("xpath=//*[@id='undefined']/div/a/label","//*[@id='undefined']/ul/li[8]/a/label");
        sleep(5);

        $this->click("xpath=(//a[contains(text(),'отправить')])");
        sleep(5);

        // call E1.2.1 - it's good works!
        $this->click("id=icons_phone");
        sleep(15);
        $this->click("xpath=//div[@id='phoneMainScreen']/ul/li[1]");
        sleep(15);
        $this->click("xpath=(//a[contains(text(),'Позвонить')])[3]");
        sleep(15);
        $this->click("xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");
        sleep(30);

        $this->click("css=input.btn.btn-simulation-stop");
        sleep(15);
        $this->click("css=input.btn.logout");
    }
}

