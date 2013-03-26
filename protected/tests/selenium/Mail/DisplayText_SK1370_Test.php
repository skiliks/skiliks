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
class DispayText_SK1370_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1370()
    {
        $this->markTestIncomplete();
        $this->start_simulation();
        #TODO: избавится от слипа
        //здесь будет валится, т.к. в Firefox сразу после открытия письмо Крутько не отображается
        sleep(5);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(2);
        $this->checkFields("Крутько М.", "Федоров А.В.", "По ценовой политике", "Ценовая политика.xlsx");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['delete']);
        sleep(2);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['trash']);
        sleep(5);
        $this->checkFields("Крутько М.", "Федоров А.В.", "По ценовой политике", "Ценовая политика.xlsx");

        //жесть
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        //добавляем адресата
        $this->waitForVisible(Yii::app()->params['test_mappings']['mail_contacts']['krutko']);
        $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['krutko']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['krutko']);
        //тема
        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");
        //аттач 'Сводный бюджет',
        $this->addAttach('Сводный бюджет_02_v23');

        //КОПИЯ - не достучался

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['save']);
        sleep(3);
        $this->optimal_click("css=label.icon_DRAFTS");
        sleep(3);
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[1]/td","Федоров А.В.");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[2]/td","Крутько М.");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[4]/td","Сводный бюджет: файл");
        //$this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[5]/td","Ценовая политика.xlsx");

        $this->optimal_click("link=отправить черновик");
        $this->optimal_click("css=label.icon_SENDED");
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[1]/td","Федоров А.В.");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[2]/td","Крутько М.");
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[4]/td","Сводный бюджет: файл");

    }

    private function checkFields($from, $to_whom, $theme, $attach)
    {
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[1]/td", $from);
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[2]/td", $to_whom);
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[4]/td", $theme);
        $this->assertText("//div[@id='MailClient_IncomeFolder_EmailPreview']/div/table/tbody/tr[5]/td", $attach);
    }
}