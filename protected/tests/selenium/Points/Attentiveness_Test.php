<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Внимательность (Область обучения №11)
 */
class Attentiveness_Test extends SeleniumTestHelper
{
    public function test_attentiveness_max()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->optimal_click("link=F36");
        $this->optimal_click("link=F37");
        $this->run_event('T6.1',"xpath=(//*[contains(text(),'Валерий Семенович просил уточнить сколько копий презентаций и к какому числу необходимо сделать.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Среда, 17.00, у вас в коробках, сорок копий. Спасибо!')])");

        $this->run_event('T6.2',"xpath=(//*[contains(text(),'Марина, запиши, пожалуйста, важную информацию по презентации')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Нет-нет. Ее надо распечатать. Запиши, пожалуйста')])");
        $this->optimal_click("xpath=(//*[contains(text(),'По-моему, в семнадцать')])");
        sleep(10);

        $this->run_event('ET12.4',"xpath=(//*[contains(text(),'Ну как')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В этот раз точно лучше, чем в прошлый')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович, мы все ваши замечания учли')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Цветовая гамма')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, сам. Это ведь высший приоритет')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Правильно я понял, в презентации оставляем все как есть')])");

        $this->run_event('MS20');
        sleep(2);
        $this->run_event('MS39');
        sleep(2);
        $this->run_event('MS48');
        sleep(2);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal11'],"100");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal11'],"100");
        $this->stop();
    }
}
