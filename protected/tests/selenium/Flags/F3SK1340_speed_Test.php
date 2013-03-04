<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 2/25/13
 * Time: 5:51 PM
 * To change this template use File | Settings | File Templates.
 */
class FlagsF3SK1340Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1340() {
        // next line for not running the test
        $this->markTestIncomplete();
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
}

