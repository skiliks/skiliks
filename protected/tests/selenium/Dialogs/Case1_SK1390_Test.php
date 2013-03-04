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
        $this->markTestIncomplete(); //пока не починят баг с оценкой
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

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        #TODO: заменить!
        sleep(15);
        $this->assertText("//table[6]/tbody/tr/td[2]","5.416667");
        $this->assertText("//table[6]/tbody/tr[2]/td[2]","0");
        $this->assertText("//table[6]/tbody/tr[3]/td[2]","4");
    }
}
