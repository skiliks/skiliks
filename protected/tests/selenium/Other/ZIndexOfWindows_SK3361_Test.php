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
        $this->start_simulation("test_ZIndexOfWindows_SK3361");

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['todo']);
        sleep(5);
        $this->assertTrue($this->isVisible("css=.plan-todo-tit-hide"));
        $a_index=$this->getEval("window.document.getElementById('plan-window').style.zIndex")+0;

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(5);
        $this->assertTrue($this->isVisible("css=.MOVE_TO_TRASH"));
        $b_index=$this->getEval("window.document.getElementById('mail-window').style.zIndex")+0;

        $this->assertTrue($a_index<$b_index);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(5);
        $this->assertTrue($this->isVisible("css=.phone-menu-btn.phone_get_menu"));
        $c_index=$this->getEval("window.document.getElementById('phone-window').style.zIndex")+0;

        $this->assertTrue($b_index<$c_index);

        $this->simulation_showLogs();
    }
}