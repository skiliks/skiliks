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


        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->type("id=addTriggerSelect", "E1.2");
        $this->click("xpath=//form/fieldset/div[2]/div/input[2]");
        $this->waitForVisible("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->click("xpath=(//*[contains(text(),'— Закончила? Теперь слушай сюда.')])");
        sleep(10);

        $this->click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->click(Yii::app()->params['test_mappings']['mail']['to_whom']);

        $this->waitForElementPresent($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        sleep(15);
        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        sleep(2);
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        sleep(2);
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        sleep(2);
        $this->click("xpath=(//a[contains(text(),'отправить')])");

        // change time to 13:00
        $this->click("xpath=(//*[contains(text(),'13:00')])");

        // call E1.2.1 - it's good works!
        $this->click("id=icons_phone");
        //$this->click("xpath=//div[@class='phone icon_active']");
        sleep(2);
        $this->click("xpath=//div[@id='phoneMainScreen']/ul/li[1]");
        sleep(2);
        $this->click("xpath=(//a[contains(text(),'Позвонить')])[3]");
        sleep(2);
        $this->click("xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");
        sleep(15);

        // some replics from dialog E1.2.1
        //$this->assertTextPresent("Марина, ну как у");
        //

        // verifing the value of F3'
        $this->assertText("xpath=//div[@class='debug-panel']/div[@class='row']/div[@class='span3'][2]/form[@class='form-inline form-flags']/fieldset/table[@class='table table-bordered'][2]/tbody/tr/td[5]","1");
        sleep(2);

        $this->click("css=input.btn.btn-simulation-stop");
    }
}

