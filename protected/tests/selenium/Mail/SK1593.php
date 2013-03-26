<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Case 1.
 * Запускаем Е2.8. Получаем от Крутько драфт версию презентации (D4).
 * Сохраняем ее и отрпавляем ГД. Убеждаемся что залогировалось письмо MS25
 *
 * Case 2.
 * Активируем F14. Получаем от Крутько итоговую версию презентации (D5).
 * Сохраняем ее и отрпавляем ГД. Убеждаемся что залогировалось письмо MS83
 *
 * Case 3.
 * Активируем F16. Получаем от Крутько рабочую версию презентации (D6)
 * Сохраняем ее и отрпавляем ГД. Убеждаемся что залогировалось письмо MS84
 */
class SK1593_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1593_MS25()
    {
        $this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('E2.8', "xpath=(//*[contains(text(),'Иди к себе и пришли мне все')])", "click");
        //$this->optimal_click("xpath=(//*[contains(text(),'Иди к себе и пришли мне все')])");
        $this->save_send();
        $this->waitForVisible("//table[@class='table table-striped mail-log']//td[3]");
        $this->assertTrue("//table[@class='table table-striped mail-log']//td[3]",'MS25');
    }

    public function testSK1593_MS83()
    {
        $this->markTestIncomplete();
        $this->start_simulation();
        sleep(10);
        $this->optimal_click('link=F14');
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "M10");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);
        $this->save_send();
        $this->waitForVisible("//table[@class='table table-striped mail-log']//td[3]");
        $this->assertTrue("//table[@class='table table-striped mail-log']//td[3]",'MS83');
    }

    public function testSK1593_MS84()
    {
        $this->markTestIncomplete();
        $this->start_simulation();
        sleep(10);
        $this->optimal_click('link=F16');
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "M9");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);
        $this->save_send();
        $this->waitForVisible("//table[@class='table table-striped mail-log']//td[3]");
        $this->assertTrue("//table[@class='table table-striped mail-log']//td[3]",'MS84');
    }

    private function save_send()
    {
        $this->optimal_click("css=li.icon-active.mail a");
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Презентация для ГД')])");
        $this->optimal_click("css=span.save-attachment-icon");
        $this->optimal_click("//div[@class='mail-popup-button']/div");
        sleep(15);

        //пишем письмо
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['mail_contacts']['boss']);
        $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['boss']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['boss']);
        //тема
        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=(//*[contains(text(),'Презентация на выставку')])");
        //аттач
        $this->addAttach('Презентация_ ГД_01');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
    }
}