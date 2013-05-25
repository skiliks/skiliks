<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Устойчивость к манипуляциям и давлению (Область обучения №10)
 */
class Resistance_to_manipulation_Test extends SeleniumTestHelper
{

    public function test_resistance_max()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E15',"xpath=(//*[contains(text(),'Раиса Романовна, прошу прощения')])",'click');
        sleep(7);

        $this->run_event('RS2',"xpath=(//*[contains(text(),'Приветствую, Егор!  У тебя что-то срочное')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Какой план? Я бюджетом занят')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, Егор! До отпуска времени у меня нет')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Егор,  ты совершенно напрасно волнуешься')])");
        sleep(7);

        $this->run_event('RV1',"xpath=(//*[contains(text(),'А вы по какому вопросу')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давайте свой наряд, я напишу отказ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Сегодня и завтра этот кабинет занят')])");
        sleep(10);

        $this->run_event('E2', "xpath=(//*[contains(text(),'Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать!')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, у меня в графике уже выделено время на проверку')])");

        $this->run_event('E3.1',"xpath=(//*[contains(text(),'Здравствуйте. Любопытно. И какое отношение этот проект имеет ко мне')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Извините, давайте созвонимся после отпуска, я сейчас очень занят')])");

        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, файл готовил не я, а Трутнев')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'могу я переслать корректировки завтра утром')])");
        sleep(7);


        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal10'],"100");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal10'],"100");
    }
}
