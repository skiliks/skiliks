<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 2/25/13
 * Time: 5:51 PM
 * To change this template use File | Settings | File Templates.
 */
class FlagsSK1338Test extends CWebTestCase
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1338() {
        // next line for not running the test
        //$this->markTestIncomplete();

        $this->deleteAllVisibleCookies();
        $this->open('/site/');
        $this->setSpeed("2000");
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
        $this->click("id=icons_email");
        sleep(15);
        //$this->click("link=новое письмо");
        //sleep(15);
        $this->click("id=MailClient_RecipientsList");
        sleep(15);

        $this->waitForVisible("xpath=(//*[contains(text(),'Крутько')])");
        sleep(15);
        $this->mouseOver("xpath=(//*[contains(text(),'Крутько')])");
        sleep(15);
        $this->click("xpath=(//*[contains(text(),'Крутько')])");
        sleep(15);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        sleep(5);

        // sometimes there is a 500 error, that's why next 2 rows nedd to be uncomment
        // $this->click("//div[@class='mail-popup']//td[1]/div['Продолжить']");
        // sleep(5);

        $this->click("xpath=//*[@id='undefined']/div/a");
        sleep(15);
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        sleep(15);
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        sleep(15);
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        sleep(5);

        $this->click("xpath=(//a[contains(text(),'отправить')])");
        sleep(5);

        // change time to 13:50
        $this->click("xpath=(//*[contains(text(),'13:00')])");
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

        // some replics from dialog E1.2.1

        //
        // verifing the value of F3
        $this->assertText("xpath=//div[@class='debug-panel']/div[@class='row']/div[@class='span3'][2]/form[@class='form-inline form-flags']/fieldset/table[@class='table table-bordered'][2]/tbody/tr/td[4]","1");
        //

        $this->click("xpath=(//*[contains(text(),'Марина, ну как у')])");
        $this->click("xpath=(//*[contains(text(),'Я про сводный')])");
        $this->click("xpath=(//*[contains(text(),'Отлично, и сразу')])");
        sleep(15);

        $this->click("css=input.btn.btn-simulation-stop");
        sleep(15);
        $this->click("css=input.btn.logout");
    }
}

