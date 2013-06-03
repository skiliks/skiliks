<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Case 1.
 * Запускаем Е2.8. Получаем от Крутько драфт версию презентации (D4).
 * Сохраняем ее и отрпавляем ГД.
 * После чего отправляем такое же письмо, но без аттача (SK1686 Descrptn 1.3)
 * Убеждаемся что залогировалось письмо MS25 и "пустышка"
 *
 * Case 2.
 * Активируем F14. Получаем от Крутько итоговую версию презентации (D5).
 * Сохраняем ее и отрпавляем ГД. Убеждаемся что залогировалось письмо MS83
 *
 * Case 3.
 * Активируем F16. Получаем от Крутько рабочую версию презентации (D6)
 * Сохраняем ее и отрпавляем ГД. Убеждаемся что залогировалось письмо MS84
 */
class Receiving_attachments_SK1593_Test extends SeleniumTestHelper
{

    public function test_Receiving_attachment_MS25_SK1593()
    {
        $mail_code = array('M11','MS25','M11','','M11');
        $window = array('mail main','mail new','mail main','mail new','mail main');
        $ms25 = array($window, $mail_code);
        $this->start_simulation();
        sleep(5);
        $this->run_event('E2.8', "xpath=(//*[contains(text(),'Иди к себе и пришли мне все')])", "click");
        sleep(30);
        $this->save_send();
        sleep(30);
        //отправка того же письма без аттача
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        sleep(30);
        $this->waitForVisible(Yii::app()->params['test_mappings']['mail_contacts']['boss']);
        $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['boss']);
        sleep(30);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['boss']);
        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        sleep(30);
        $this->optimal_click("xpath=(//*[contains(text(),'Презентация на выставку')])");
        sleep(30);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        sleep(30);
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        sleep(30);
        $this->Mail_log($ms25, sizeof($window));
        $this->close();
    }

    public function test_Receiving_attachment_MS83_SK1593()
    {

        $mail_code = array('M11','MS83','M11');
        $window = array('mail main','mail new','mail main');
        $ms83 = array($window, $mail_code);
        $this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->optimal_click('link=F14');
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "M10");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);
        $this->save_send();
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->Mail_log($ms83, sizeof($window));
        $this->close();
    }

    public function test_Receiving_attachment_MS84_SK1593()
    {
        $mail_code = array('M11','MS84','M11');
        $window = array('mail main','mail new','mail main');
        $ms84 = array($window, $mail_code);
        $this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->optimal_click('link=F16');
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "M9");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);
        $this->save_send();
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->Mail_log($ms84, sizeof($window));
        $this->close();
    }

    private function save_send()
    {
        $this->optimal_click("css=li.icon-active.mail a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Презентация для ГД')])");
        $this->optimal_click("css=span.save-attachment-icon");
        $this->optimal_click("//div[@class='mail-popup-button']/div");
        sleep(20);

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
        $this->close();

    }
}