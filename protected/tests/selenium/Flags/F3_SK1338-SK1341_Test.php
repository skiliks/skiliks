<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 3/4/13
 * Time: 9:48 PM
 * To change this template use File | Settings | File Templates.
 */
class F3_SK1338_1341_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1338_Case1() {
        // next line for not running the test
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.2');

        $this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'— Закончила? Теперь слушай сюда.')])");

        $krutko=Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->write_mail_active();
        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");

        // change time to 13:00
        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);

        // some replics from dialog E1.2.1
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");
        $this->waitForElementPresent("xpath=(//a[contains(text(),'Марина, ну как у')])");
        $this->assertTextPresent("Марина, ну как у");
        //
        // verifing the value of F3
        $this->assertText("xpath=//div[@class='debug-panel']/div[@class='row']/div[@class='span3'][2]/form[@class='form-inline form-flags']/fieldset/table[@class='table table-bordered'][2]/tbody/tr/td[5]","1");
        $this->click("css=input.btn.btn-simulation-stop");
    }

    public function testSK1339_Case2() {
        // next line for not running the test
        //$this->markTestIncomplete();
        $this->start_simulation();

        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->run_event('E1.2');

        $this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Пусть спрашивает')])");

        $krutko=Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->write_mail_active();
        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");

        // change time to 13:00
        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);

        // call E1.2.1 - it's good works!
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");
        $this->waitForElementPresent("xpath=(//a[contains(text(),'Марина, ну как у')])");
        $this->assertTextPresent("Марина, ну как у");

        // verifing the value of F3
        $this->assertText("xpath=//div[@class='debug-panel']/div[@class='row']/div[@class='span3'][2]/form[@class='form-inline form-flags']/fieldset/table[@class='table table-bordered'][2]/tbody/tr/td[5]","1");
        $this->click("css=input.btn.btn-simulation-stop");
    }

    public function testSK1340_Case3() {
        // next line for not running the test
        //$this->markTestIncomplete();
        $this->start_simulation();

        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->run_event('E1.2');

        $this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ты же у нас такая талантливая и умная!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты будешь выполнять только одну задачу')])");

        $krutko=Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->write_mail_active();
        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");

        // change time to 13:00
        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);

        // call E1.2.1 - it's good works!
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");
        $this->waitForElementPresent("xpath=(//a[contains(text(),'Марина, ну как у')])");
        $this->assertTextPresent("Марина, ну как у");

        //
        // verifing the value of F3
        $this->assertText("xpath=//div[@class='debug-panel']/div[@class='row']/div[@class='span3'][2]/form[@class='form-inline form-flags']/fieldset/table[@class='table table-bordered'][2]/tbody/tr/td[5]","1");
        $this->click("css=input.btn.btn-simulation-stop");
    }

    public function testSK1341_Case4() {
        // next line for not running the test
        //$this->markTestIncomplete();
        $this->deleteAllVisibleCookies();
        $this->open('/site/');
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

        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->waitForVisible(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(2);

        $this->waitForVisible("xpath=//*[contains(text(),'новое письмо')]");
        sleep(2);
        $this->click("xpath=//*[contains(text(),'новое письмо')]");
        sleep(2);

        $this->waitForVisible(Yii::app()->params['test_mappings']['mail']['to_whom']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['mail']['to_whom']);

        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");

        // change time to 13:00
        $this->click("xpath=(//*[contains(text(),'13:00')])");
        // call E1.2.1 - it's good works!
        $this->click("id=icons_phone");
        $this->waitForElementPresent("xpath=//div[@id='phoneMainScreen']/ul/li[1]");
        $this->click("xpath=//div[@id='phoneMainScreen']/ul/li[1]");
        $this->waitForElementPresent("xpath=(//a[contains(text(),'Позвонить')])[3]");
        $this->mouseOver("xpath=(//a[contains(text(),'Позвонить')])[3]");
        $this->click("xpath=(//a[contains(text(),'Позвонить')])[3]");
        $this->waitForElementPresent("xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");
        $this->mouseOver("xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");
        $this->click("xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");

        $this->assertTextNotPresent("Марина, ну как у");
        //
        // verifing the value of F3
        $this->assertText("xpath=//div[@class='debug-panel']/div[@class='row']/div[@class='span3'][2]/form[@class='form-inline form-flags']/fieldset/table[@class='table table-bordered'][2]/tbody/tr/td[5]","0");
        $this->click("css=input.btn.btn-simulation-stop");
    }
}

