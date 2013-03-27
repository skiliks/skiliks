<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 *
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
        //$this->markTestIncomplete();
        $this->start_simulation();


        $m = array('main screen','mail','mail','mail', 'plan', 'mail');
        $s= array('main screen','mail main','mail main','mail new','plan','mail new');
        $TH = array($s, $m);

        $m1 = array('MY1','','');
        $s1= array('mail main','mail new','mail new');
        $TH1 = array($s1, $m1);

        $this->write_email();

        $krutko=Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->optimal_click($krutko);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['todo']);

        sleep(1);

        $this->optimal_click("css=.sim-window.planner-book-main-div .sim-window-content > div .btn-close button");

        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");

        $this->addAttach('Сводный бюджет_02_v23');
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'сохранить')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['sim_points']);
        sleep(20);
        $this->Universal($TH);
        $this->Mail_log($TH1);
        $this->Leg_actions_detail();
        $this->Leg_actions_aggregated();


    }
}