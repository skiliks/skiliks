<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vad
 * Date: 27.02.13
 * Time: 16:51
 * To change this template use File | Settings | File Templates.
 */

class Case1_SK1390_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1390()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('ET1.1');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Не вижу сводного бюджета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна,  я как раз собираюсь')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я пока не знаю, сколько времени мне потребуется')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понял, открываю файл')])");

        $this->run_event('ET2.1');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->run_event('ET2.3');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Непременно, сейчас запланирую время на проверку')])");

        sleep(5);
        #TODO: сделать без привязки к таблице
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("id=assessment-results")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->assertText("//table[4]/tbody/tr[6]/td[4]","0.666667");
        $this->assertText("//table[4]/tbody/tr[17]/td[4]","1");
        $this->assertText("//table[4]/tbody/tr[14]/td[4]","1");
        $this->assertText("//table[4]/tbody/tr[8]/td[4]","2");
        $this->assertText("//table[4]/tbody/tr[9]/td[4]","2");
        $this->assertText("//table[4]/tbody/tr[16]/td[4]","1");
        $this->assertText("//table[4]/tbody/tr[20]/td[4]","1");
    }
}
