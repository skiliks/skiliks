<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для проверки правильности логирования по задаче SK1278 (логирование переключения окон Mail new при написании нового письма)
 */
class Logging_Case_SK1278_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1278()
    {
        $this->markTestIncomplete();
        $this->start_simulation();

        // первый предположительный список, который может появится в юниверсал логах
        $m = array('main screen','mail','mail','mail', 'plan', 'mail','mail');
        $s= array('main screen','mail main','mail main','mail new','plan','mail new','mail main');
        $TH = array($s, $m);
        // второй предположительный список, который может появится в юниверсал логах
        $m2 = array('main screen','mail','mail','mail','mail','mail', 'plan', 'mail','mail');
        $s2= array('main screen','mail main','mail main','mail main','mail main','mail new','plan','mail new','mail main');
        $TH2 = array($s2, $m2);
        // список, который может появится в мейл логах
        $m1 = array('MY2','','','MY2');
        $s1= array('mail main','mail new','mail new','mail main');
        $TH1 = array($s1, $m1);

        $this->write_email();

        $krutko=Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->optimal_click($krutko);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['todo']);

        sleep(3);

        $this->optimal_click("css=.sim-window.planner-book-main-div .sim-window-content > div .btn-close button");
        sleep(2);
        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");
        sleep(2);
        $this->addAttach('Сводный бюджет_02_v23');
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'сохранить')])");
        sleep(2);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['sim_points']);
        sleep(5);

        // выполняем проверку первого списка в Юниверсал логах, передаем в юниверсал список и размер одного из массивов
        $a = $this->Universal($TH, sizeof($m));
        // выполняем проверку второго списка в Юниверсал логах, передаем в юниверсал список и размер одного из массивов
        $b = $this->Universal($TH2, sizeof($m2));
        // проверяем есть хотя бы одна проверка вернула true значит все ок и продолжнаем проверку далее
        if (($a==true)||($b==true))
        {
            $this->Mail_log($TH1);
            $this->Leg_actions_detail();
            $this->Leg_actions_aggregated();
        }
        else
        {
            $this->fail("Universal logs doesn't match with expected results!!!");
        }

    }
}