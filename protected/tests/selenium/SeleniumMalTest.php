<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vad
 * Date: 2/24/13
 * Time: 5:26 PM
 */
class SeleniumMailTest extends CWebTestCase
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function test_Mail_new_first_to_deleted() {

        $this->deleteAllVisibleCookies();
        $this->open('/site/');
        $this->setSpeed("1000");
        $this->waitForVisible('id=login');
        $this->type("id=login", "vad");
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

        //маппинги Трудякина и Крутько в выпадающем списке адресатов
        $trudyakin = Yii::app()->params['test_mappings']['mail_contacts']['trudyakin'];
        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        $this->waitForElementPresent($trudyakin);
        $this->mouseOver($trudyakin);
        $this->click($trudyakin);
	    $this->click(Yii::app()->params['test_mappings']['mail']['add_recipient']);
        $this->mouseOver($krutko);
        $this->click($krutko);
        $this->select("css=select.origin", "Срочно жду бюджет логистики");
        $this->click(Yii::app()->params['test_mappings']['mail']['del_recipient']);
        $this->click(Yii::app()->params['test_mappings']['mail']['button_to_continue']);
        $this->select("css=select.origin", "Сводный бюджет: файл");

        $this->assertFalse($this->isTextPresent('Срочно жду бюджет логистики'));

        $this->click("css=input.btn.btn-simulation-stop");
        sleep(15);
        $this->click("css=input.btn.logout");
    }
}

