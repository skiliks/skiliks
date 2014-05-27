<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки наличия списка всех входящих и исходящих сообщений
 */
class Pause_SK3359_Test extends SeleniumTestHelper
{
    //for end of simulation
    public function test_Pause_Case1_SK3359()
    {
        $this->start_simulation("test_Pause_Case1_SK3359");
        sleep(3);
        // перемотать время на 18:00
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], '18');
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], '00');
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        sleep(5);
        // считать текущее время
        $time=$this->how_much_time();
        sleep(30);
        // сичтать время, пока игра стоит на паузе ( и проверить что время не меняется)
        $time1=$this->how_much_time();

        $this->assertTrue($time==$time1);

        $this->optimal_click("xpath=(//*[contains(text(), 'Продолжить работу')])");
        // проверить, что сразу после продолжения работы время такое же как и во время показа попапа о завершении игры
        $time2=$this->how_much_time();
        $this->assertTrue($time==$time2);

        $this->simulation_stop();
    }

    //for lite version
    public function test_Pause_Case2_SK3359()
    {
        $this->setUp();
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');
        // логинимся и запускаем демо-версию
        $this->optimal_click("css=.action-sign-in");
        $this->waitForVisible("css=#YumUserLogin_username");
        $this->type("css=#YumUserLogin_username", "selenium.engine@skiliks.com");
        $this->type("css=#YumUserLogin_password", "skiliks123123");
        $this->optimal_click("name=yt0");
        for ($second = 0; ; $second++) {
            if ($second >= 600) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=(//*[contains(text(),'Рабочий')])")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }

        $this->createCookie("intro_is_watched=yes", "path=/, expires=365");
        $this->open('/simulation/developer/lite');

        for ($second = 0; ; $second++) {
            if ($second >= 600) $this->fail("timeout");
            try {
                if ($this->isVisible(Yii::app()->params['test_mappings']['icons']['mail'])) break;
            } catch (Exception $e) {}
            usleep(100000);
        }
        $this->getEval('var window = this.browserbot.getUserWindow(); window.$(window).off("beforeunload")');
        sleep(10);
        // ставим игру на паузу
        $time3=$this->how_much_time();
        $this->optimal_click("css=.pause");
        sleep(30);
        // пока игра на паузе считываем время и проверяем, что оно не изменилось
        $time4=$this->how_much_time();
        $this->assertTrue($time3==$time4);

        $this->optimal_click("xpath=(//*[contains(text(), 'Вернуться к симуляции')])");
        $time5=$this->how_much_time();
        $this->assertTrue($time3==$time5);

        $this->simulation_stop();
    }
}