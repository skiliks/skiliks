<?php
class F22_SK1428_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1428()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('T7.3');
        $this->optimal_click("xpath=(//*[contains(text(),'Я по поводу задания от логистов. Поговорил с Трудякиным.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, отложи все дела и сделай срочно')])");

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[4]");
        #TODO: заменить!
        sleep(10);
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[4]","1");

        $this->run_event('T7.4');
        $this->optimal_click("xpath=(//*[contains(text(),'Я по поводу задания от логистов')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Данные у вас в почте. Только что отправил')])");
        $this->optimal_click("css=li.icon-active.mail a");
        $this->optimal_click("//table[@id='mlTitle']/tbody/tr[5]/td[2]");
        $this->verifyTextPresent("Вот, сделал. Смотрите. \nС уваженим, Трутнев С.");
    }

    public function testSK1428_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[4]");
        #TODO: заменить!
        sleep(10);
        $this->assertText("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[4]","0");

        $this->run_event('T7.4');
        $this->optimal_click("xpath=(//*[contains(text(),'Я по поводу задания от логистов')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Данные у вас в почте. Только что отправил')])");

        $this->optimal_click("css=li.icon-active.mail a");
        $this->optimal_click("//table[@id='mlTitle']/tbody/tr[5]/td[2]");
        $this->verifyTextPresent("exact:Добрый день! Заполнил вашу форму. Есть ли вопросы? \nВсего доброго, Трутнев С.");
    }
}