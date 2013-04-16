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
        //TODO: сейчас здесь выдает 500, т.к. есть баг (который возникает при быстрых кликах по диалогу)

        // здесь будут совсем другие значения для юниверсал лог
        /*$m = array('main screen','phone','main screen','phone', 'main screen','phone', 'main screen','phone', 'main screen','phone');
        $s = array('main screen','phone call','main screen','phone talk', 'main screen','phone call', 'main screen','phone call', 'main screen','phone talk');
        $TH = array($s, $m);*/


/*        $m1 = array('MS27','MS48','MS68','MS70');
        $s1= array('mail new','mail new','mail new','mail new');
        $TH1 = array($s1, $m1);*/

        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию для Генерального')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше. Жду в 15.30')])");

        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В понедельник, скажем в 10.00, будет моя сотрудница Марина Крутько')])");
        // отут треба глянути, тому що з послідовністю фраз якась фігня
        $this->run_event('E12.5',"xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])",'click');

        $this->optimal_click("xpath=(//*[contains(text(),'Кхе-кхе…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, доволен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ваша презентация была не единственным его промахом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это наши корпоративные цвета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы вместе с сотрудниками. Они готовили – я проверял.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошего вам выступления, Валерий Семенович!')])");
        sleep(10);

        $this->run_event('MS27');
        sleep(2);
        $this->run_event('MS48');
        sleep(2);
        $this->run_event('MS68');
        sleep(2);
        $this->run_event('MS70');
        sleep(2);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        //$this->Universal($TH);
/*      $this->Mail_log($TH1);
        $this->Leg_actions_detail();
        $this->Leg_actions_aggregated();*/
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        $this->checkSimPoints('5.833','-7');
        $this->checkLearningArea('4.27','0.00','12.5','0.00','8.82','5','0.00','20');


    }
}
