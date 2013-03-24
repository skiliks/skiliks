<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест на диалоги, Case 3.
 * Пошагово запускаем диалоги из /Users/Tony/Dropbox/projectx - development/1. Documentation/1.1 Scenario/1.1.1 Оценка/Тесты/Расчет оценки_тест3_final.xls
 * После чего в Simulation points сверяем суммы оценок поведений positive, negative & personal по mail matrix и all dialogs
 */
class Case3_SK910_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK910()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию для Генерального')])",'click');
        //$this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию для Генерального')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше. Жду в 15.30')])");

        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])",'click');
        //$this->optimal_click("xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'В понедельник, скажем в 10.00, будет моя сотрудница Марина Крутько')])");

        $this->run_event('E12.5',"xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])",'click');
        //$this->optimal_click("xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])");

        $this->optimal_click("xpath=(//*[contains(text(),'Кхе-кхе…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, доволен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ваша презентация была не единственным его промахом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это наши корпоративные цвета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы вместе с сотрудниками. Они готовили – я проверял.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошего вам выступления, Валерий Семенович!')])");

        $this->run_event('MS27',"xpath=(//*[contains(text(),'октября')])",'-');
        sleep(3);
        $this->run_event('MS48',"xpath=(//*[contains(text(),'октября')])",'-');
        sleep(3);
        $this->run_event('MS68',"xpath=(//*[contains(text(),'октября')])",'-');
        sleep(3);
        $this->run_event('MS70',"xpath=(//*[contains(text(),'октября')])",'-');
        sleep(3);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['sim_points']);
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_positive'],"8.083");
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_negative'],"-7");
        $this->assertText(Yii::app()->params['test_mappings']['dev']['admm_personal'],"6.5");
    }
}
