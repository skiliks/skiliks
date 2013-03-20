<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на тестирование отображение реплик, которые не зависят от флага (для SK1623)
 */
class Case_SK1623_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     *
     */
    public function testSK1623_Case1() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(2);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->run_event('ET12.1');

        $this->optimal_click("css=li.icon-active.phone a");

        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Не ответить')])"));

        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));

    }


    public function testSK1623_Case2() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(2);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->optimal_click("xpath=//div/div[4]/form[1]/fieldset/table[1]/thead/tr/th[6]/a");

        $this->assertTrue($this->verify_flag('F14','1'));

        $this->run_event('ET12.1');

        $this->optimal_click("css=li.icon-active.phone a");

        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Не ответить')])"));

        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));

    }

    public function testSK1623_Case3() {

        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->assertTrue($this->verify_flag('F14','0'));

        $this->optimal_click("xpath=//div/div[4]/form[1]/fieldset/table[1]/thead/tr/th[6]/a");

        $this->assertTrue($this->verify_flag('F14','1'));

        $this->run_event('ET12.2');
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));

    }


    public function testSK1623_Case4() {

        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->assertTrue($this->verify_flag('F14','0'));

        $this->run_event('ET12.2');
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));

    }

}
