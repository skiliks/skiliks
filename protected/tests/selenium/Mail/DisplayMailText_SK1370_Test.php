<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест, проверяющий отображение текста писем и полей "От кого", "Кому", "Копия", "Тема", "Вложение"
 * 1) На вкладке "Входящие" у одного письма проверяем все поля и текст.
 * 2) Удаляем письмо, проверяем то же самое в корзине.
 * 3) Пишем письмо с копией и вложением, сохраняем в черновик
 * 4) Проверяем поля в черновике.
 * 5) Отправляем из черновика, проверяем поля во вкладке "Отправленные".
 */
class DisplayMailText_SK1370_Test extends SeleniumTestHelper
{
    public function test_DisplayMailText_SK1370()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("DisplayMailText_SK1370_Test");
        $this->optimal_click('link=F32');
        sleep(5);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(2);
        $this->optimal_click("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->checkFields("Крутько М.", "Федоров А.В.", "По ценовой политике", "Ценовая политика_v1.pptx");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['delete']);
        sleep(2);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['trash']);
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->checkFields("Крутько М.", "Федоров А.В.", "По ценовой политике", "Ценовая политика_v1.pptx");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        sleep(5);

        //добавляем адресата
        $this->addRecipient("xpath=(//*[contains(text(),'Крутько')])");

        //тема
        $this->addTheme("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");

        //аттач 'Сводный бюджет',
        $this->addAttach('Сводный бюджет_2014_план');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['save']);
        sleep(2);
        $this->optimal_click("css=label.icon_DRAFTS");
        sleep(2);
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[1]/td","Федоров А.В.");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[2]/td","Крутько М.");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[4]/td","Сводный бюджет: файл");

        $this->optimal_click("link=отправить черновик");
        sleep(5);
        $this->optimal_click("css=label.icon_SENDED");
        sleep(2);
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[1]/td","Федоров А.В.");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[2]/td","Крутько М.");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[4]/td","Сводный бюджет: файл");

        $this->simulation_showLogs();
    }

    private function checkFields($from, $to_whom, $theme, $attach)
    {
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[1]/td", $from);
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[2]/td", $to_whom);
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[4]/td", $theme);
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[5]/td", $attach);
    }
}