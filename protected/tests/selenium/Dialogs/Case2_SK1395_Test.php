<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест на диалоги, Case 2.
 * Пошагово запускаем диалоги из /Users/Tony/Dropbox/projectx - development/1. Documentation/1.1 Scenario/1.1.1 Оценка/Тесты/Расчет оценки_тест3_final.xls
 * После чего в Simulation points сверяем суммы оценок поведений positive, negative & personal по mail matrix и all dialogs
 */
class Case2_SK1395_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1395()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(2);
        $this->run_event('E1',"xpath=(//*[contains(text(),'Раиса Романовна, ну что вы так волнуетесь?! Я уже несколько дней')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь')])");
        sleep(2);
        $this->run_event('E8.3',"xpath=(//*[contains(text(),'Нет, прости, Мирон. Сегодня просто сумасшедший день.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Семен, а у тебя наверняка в бюджете статейка есть на непредвиденные расходы')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас вернусь и напишу служебку. Спасибо за информацию! ')])");
        sleep(2);
        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, буду в 18.00')])");
        sleep(3);
        $this->run_event('E12.4',"xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])", 'click');
        sleep(3);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['sim_points']);
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_positive'],"6.5");
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_negative'],"0");
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_personal'],"9");
    }
}
