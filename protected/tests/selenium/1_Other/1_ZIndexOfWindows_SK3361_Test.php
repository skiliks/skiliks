<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки открытия каждого нового окна поверх предыдущего (для SK3361))
 */
class ZIndexOfWindows_SK3361_Test extends SeleniumTestHelper
{
    public function test_ZIndexOfWindows_SK3361 ()
    {
        $this->start_simulation();

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['todo']);
        sleep(5);
        $this->assertTrue($this->isVisible("css=.plan-todo-tit-hide"));
        $a_index=$this->getEval("window.document.getElementById('plan-window').style.zIndex");

        /*$this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(5);
        $this->assertTrue($this->isVisible("css=.MOVE_TO_TRASH"));
        $b_index=$this->getEval("window.document.getElementById('mail-window').style.zIndex");

        $this->assertTrue($a_index<$b_index);*/

        $this->simulation_stop();
    }
}