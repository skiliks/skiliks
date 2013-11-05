<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для отображения реплик, от флага никак НЕ зависящих (для SK1623)
 */
class IndependentReplicas_SK1623_Test extends SeleniumTestHelper
{
    /**
     * testSK1623_Case1() тестирует задачу SKILIKS-1623 для реплики в диалоге ET12.1, появление которой не зависит от флага (флаг не включен)
     */
    public function test_IndependentReplicas_SK1623_Case1() {

        //$this->markTestIncomplete();
        $this->start_simulation("test_IndependentReplicas_SK1623_Case1");
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->run_event('ET12.1',Yii::app()->params['test_mappings']['icons_active']['phone'],'click');
        $this->assertElementPresent(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertElementPresent("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])");
        $this->simulation_showLogs();
    }

    /**
     * testSK1623_Case2() тестирует задачу SKILIKS-1623 для реплики в диалоге ET12.1, появление которой не зависит от флага (флаг включен)
     */
    public function test_IndependentReplicas_SK1623_Case2() {

        //$this->markTestIncomplete();
        $this->start_simulation("test_IndependentReplicas_SK1623_Case2");
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));
        $this->assertTrue($this->verify_flag('F36','0'));

        $this->optimal_click(Yii::app()->params['test_mappings']['flags']['F14']);
        $this->optimal_click(Yii::app()->params['test_mappings']['flags']['F36']);

        $this->run_event('ET12.1',Yii::app()->params['test_mappings']['icons_active']['phone'],'click');

        $this->assertElementPresent(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);

        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertElementPresent("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])");
        $this->simulation_showLogs();
    }


    /**
     * testSK1623_Case3() тестирует задачу SKILIKS-1623 для реплики в диалоге ET12.2, появление которой не зависит от флага (флаг включен)
     */
    public function test_IndependentReplicas_SK1623_Case3() {

        //$this->markTestIncomplete();
        $this->start_simulation("test_IndependentReplicas_SK1623_Case3");
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));
        $this->assertTrue($this->verify_flag('F36','0'));

        $this->optimal_click(Yii::app()->params['test_mappings']['flags']['F14']);
        $this->optimal_click(Yii::app()->params['test_mappings']['flags']['F36']);

        $this->run_event('ET12.2');
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));
        $this->simulation_showLogs();
    }


    /**
     * testSK1623_Case4() тестирует задачу SKILIKS-1623 для реплики в диалоге ET12.1, появление которой не зависит от флага (флаг не включен)
     */
    public function test_IndependentReplicas_SK1623_Case4() {

        //$this->markTestIncomplete();
        $this->start_simulation("test_IndependentReplicas_SK1623_Case4");
        sleep(5);
        $this->assertTrue($this->verify_flag('F14','0'));

        $this->run_event('ET12.2');
        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Валерий Семенович просит прямо сейчас')])");
        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Нет у меня никакой презентации')])"));
        $this->simulation_showLogs();
    }

}
