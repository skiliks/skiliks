<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки наличия шестеренок для настройки звука в окнах (для SK3361))
 */
class ZIndexOfWindows_SK3361_Test extends SeleniumTestHelper
{
    public function test_ZIndexOfWindows_SK3361 ()
    {
        $this->start_simulation();

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['todo']);
        sleep(5);
        $this->assertTrue($this->isVisible("css=.plan-todo-tit-hide"));
        //printf($this->getEval("$('.sim-window.mail-window').css('z-index')"));
        printf($this->getEval("window.document.getElementById('plannerBookAfterVacation').style.zIndex"));
        //printf($this->getEval("$(this.browserbot.getCurrentWindow().document).find('body')"));
        //$(this.browserbot.getCurrentWindow().document).find('body');
        /*$this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(4);
        $this->assertTrue($this->isVisible("css=.MOVE_TO_TRASH"));
        printf($this->getEval("this.browserbot.getCurrentWindow().document.getElementByClassName('sim-window mail-window ui-draggable').style.z-index"));

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(4);
        $this->assertTrue( $this->isVisible("css=.phone-menu-btn.phone_get_menu"));
        printf($this->getEval("this.browserbot.getCurrentWindow().document.getElementByClassName('sim-window ui-draggable').style.z-index"));

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['documents']);
        sleep(4);
        $this->assertTrue($this->isVisible("css=.elfinder-cwd-filename.ui-draggable"));
        printf($this->getEval("this.browserbot.getCurrentWindow().document.getElementByClassName('sim-window ui-draggable').style.z-index"));
        $this->simulation_stop();*/
    }
}