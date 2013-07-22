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
        $a_zindex=$this->getEval("window.document.getElementById('plan-window').style.z-index");

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(5);
        $this->assertTrue($this->isVisible("css=.MOVE_TO_TRASH"));
        $b_zindex=$this->getEval("window.document.getElementById('mail-window').style.z-index");

        $this->assertTrue($a_zindex<$b_zindex);

        $this->simulation_stop();
    }
}