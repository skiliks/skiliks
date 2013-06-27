<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Эффективное управлению звонками (Область обучения №6)
 */
class EmailEffectiveWork_SK2557_Test extends SeleniumTestHelper
{
    public function EmailEffectiveWork_SK2557_Test()
    {
        //$this->markTestIncomplete();

        $this->start_simulation();
        //$this->clearEventQueueBeforeEleven('RST1');



        /*$this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['calls6'],"75");
        $this->assertText(Yii::app()->params['test_mappings']['log']['calls6'],"75");*/
        $this->close();
    }
}
