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
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }
//SK2420_4
    public function test_214b_214a()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['todo']);

        //сегодня
        $this->dragAndDropToObject("//div[@data-task-id='19']","//div[@id='plannerBookToday']//td[@data-hour='10' and @data-minute='15']");
        $this->dragAndDropToObject("//div[@data-task-id='17']","//div[@id='plannerBookToday']//td[@data-hour='13' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='20']","//div[@id='plannerBookToday']//td[@data-hour='15' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='24']","//div[@id='plannerBookToday']//td[@data-hour='16' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='15']","//div[@id='plannerBookToday']//td[@data-hour='17' and @data-minute='00']");
        //нужно крутнуть скролл, иначе селениум не увидит туду лист дальше 18.00
        $this->dragAndDrop("//div[@id='plannerBookToday']//div[@class='mCSB_dragger ui-draggable']","0, +300");
        $this->dragAndDropToObject("//div[@data-task-id='25']","//div[@id='plannerBookToday']//td[@data-hour='18' and @data-minute='00']");
        //Завтра
        $this->dragAndDropToObject("//div[@data-task-id='12']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='9' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='7']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='10' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='8']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='12' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='9']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='12' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='10']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='13' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='14']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='13' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='18']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='16' and @data-minute='30']");
        //снова скролл
        $this->dragAndDrop("//div[@id='plannerBookTomorrowTimeTable']//div[@class='mCSB_dragger ui-draggable']","0, +300");
        $this->dragAndDropToObject("//div[@data-task-id='21']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='17' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='11']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='18' and @data-minute='30']");
        $this->dragAndDropToObject("//div[@data-task-id='13']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='19' and @data-minute='00']");
        $this->dragAndDropToObject("//div[@data-task-id='16']","//div[@id='plannerBookTomorrowTimeTable']//td[@data-hour='20' and @data-minute='30']");

        //После отпуска
        $this->dragAndDropToObject("//div[@data-task-id='22']","//div[@id='plannerBookAfterVacation']//div[@class='day-plan-td-slot']");
        //в 11.00 идет отправка того, что пользователь запланировал. Перематываем на 10:59, ждем
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "59");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        sleep(45);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        sleep(5);
        $this->waitForVisible("//tr[@class='learning-goal-code-214a ']/td[4]");
        $this->assertText("//tr[@class='learning-goal-code-214a ']/td[4]","100.00");
        $this->waitForVisible("//tr[@class='learning-goal-code-214b ']/td[4]");
        $this->assertText("//tr[@class='learning-goal-code-214b ']/td[4]","100.00");
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['tasks2'],"65.45");
        $this->assertText(Yii::app()->params['test_mappings']['log']['tasks2'],"65.45");
    }
}
