<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по 214a и 214b. На план можно посмотреть в задаче SK2548
 */
class Ideal_planning_Test extends SeleniumTestHelper
{
    public function test_214b_214a()
    {
        $this->start_simulation("Ideal_planning_Test");

        $this->clearEventQueueBeforeEleven('RST1');
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['todo']);
        $this->assertVisible("//div[@data-task-id='17']");
        //сегодня
        $this->dragAndDrop("//div[@class='plan-todo-wrap mCustomScrollbar _mCS_4']//div[@class='mCSB_dragger']","0, +300");
        $this->dragAndDropToObject("//div[@data-task-id='17']","//div[@id='plannerBookToday']//td[@data-hour='10' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='15']","//div[@id='plannerBookToday']//td[@data-hour='13' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='18']","//div[@id='plannerBookToday']//td[@data-hour='15' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='22']","//div[@id='plannerBookToday']//td[@data-hour='16' and @data-minute='45']");
        $this->dragAndDropToObject("//div[@data-task-id='13']","//div[@id='plannerBookToday']//td[@data-hour='17' and @data-minute='15']");
        $this->dragAndDropToObject(" //div[@data-task-id='23']","//div[@id='plannerBookToday']//td[@data-hour='18' and @data-minute='00']");
        //Завтра
        $this->dragAndDropToObject("//div[@data-task-id='10']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='9' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='5']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='10' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='6']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='12' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='7']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='12' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='8']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='13' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='12']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='13' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='16']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='16' and @data-minute='30']");
        //снова скролл
        //$this->dragAndDrop("//div[@id='plannerBookTomorrowTimeTable']//div[@class='mCSB_dragger ui-draggable']","0, +300");
        $this->dragAndDropToObject("//div[@data-task-id='19']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='17' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='9']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='18' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='11']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='19' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='14']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='20' and @data-minute='30']");
        //$this->dragAndDropToObject("//div[@data-task-id='14']","//div[@id='plannerBookAfterVacation']//div[@class='day-plan-td-slot']");

        //После отпуска
        $this->dragAndDropToObject("//div[@data-task-id='20']","//td[@data-hour='1' and @data-minute='00']");
        //в 11.00 идет отправка того, что пользователь запланировал. Перематываем на 10:59, ждем
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "59");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        sleep(45);

        $this->simulation_showLogs();
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['group_1_2']);
        $this->assertText(Yii::app()->params['test_mappings']['log']['group_1_2'],"100.00");
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['group_1_3']);
        $this->assertText(Yii::app()->params['test_mappings']['log']['group_1_3'],"100.00");
    }
}
