<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест на диалоги, Case 1.
 * Пошагово запускаем диалоги из /Users/Tony/Dropbox/projectx - development/1. Documentation/1.1 Scenario/1.1.1 Оценка/Тесты/Расчет оценки_тест3_final.xls
 * После чего в Simulation points сверяем суммы оценок поведений positive, negative & personal по mail matrix и all dialogs
 */
class Case1_SK1390_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1390()
    {
        $this->start_simulation();

        $this->run_event('ET1.1');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Не вижу сводного бюджета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна,  я как раз собираюсь')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я пока не знаю, сколько времени мне потребуется')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понял, открываю файл')])");

        $this->run_event('ET2.1');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->run_event('ET2.3');
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Непременно, сейчас запланирую время на проверку')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['sim_points']);
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_positive'],"4.667");
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_negative'],"0");
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_personal'],"4");
    }
}
