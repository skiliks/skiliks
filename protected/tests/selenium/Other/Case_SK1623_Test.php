<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для отображения реплик, от флага никак НЕ зависящих (для SK1623)
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
     * testSK1623_Case1() тестирует задачу SKILIKS-1623 для реплики в диалоге ET12.1, появление которой не зависит от флага (флаг не включен)
     */
    public function testSK1623_Case1() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->run_event('ET12.1',"css=li.icon-active.phone a",'click');

        //$this->optimal_click("css=li.icon-active.phone a");

        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'отклонить')])"));

        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));

    }


    /**
     * testSK1623_Case2() тестирует задачу SKILIKS-1623 для реплики в диалоге ET12.1, появление которой не зависит от флага (флаг включен)
     */
    public function testSK1623_Case2() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->optimal_click("xpath=//div/div[4]/form[1]/fieldset/table[1]/thead/tr/th[6]/a");

        $this->assertTrue($this->verify_flag('F14','1'));

        $this->run_event('ET12.1',"css=li.icon-active.phone a",'click');

        //$this->optimal_click("css=li.icon-active.phone a");

        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'отклонить')])"));

        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));

    }


    /**
     * testSK1623_Case3() тестирует задачу SKILIKS-1623 для реплики в диалоге ET12.2, появление которой не зависит от флага (флаг включен)
     */
    public function testSK1623_Case3() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->optimal_click("xpath=//div/div[4]/form[1]/fieldset/table[1]/thead/tr/th[6]/a");

        $this->assertTrue($this->verify_flag('F14','1'));

        $this->run_event('ET12.2');
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));

    }


    /**
     * testSK1623_Case4() тестирует задачу SKILIKS-1623 для реплики в диалоге ET12.1, появление которой не зависит от флага (флаг не включен)
     */
    public function testSK1623_Case4() {

        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->run_event('ET12.2');
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));

    }

}
