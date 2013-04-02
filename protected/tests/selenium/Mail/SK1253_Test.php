<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Окрываем почтовый клиент, открываем окно написания нового письма
 * Добавляем первого адресата (Трудякин), добавляем второго адресата (Крутько)
 * Выбираем тему письма для Трудякина. Удаляем его из адресатов, соглашаемся на очистку формы в поп-апе
 * Выбираем тему для оставшейся Крутько, убеждаемся что темы для Трудякина не отображаются
 */
class SK1253_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1253()
    {

        #TODO: убрать дублирование
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->write_email();
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);

        //проверяем темы
        $this->checkThemes();

	    $this->optimal_click(Yii::app()->params['test_mappings']['mail']['add_recipient']);
        $this->mouseOver("//ul[contains(@class,'ui-autocomplete')]/li[15]/a");
        $this->optimal_click("//ul[contains(@class,'ui-autocomplete')]/li[15]/a");

        //проверяем темы еще раз
        $this->checkThemes();

        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=(//*[contains(text(),'Срочно жду бюджет логистики')])");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['del_recipient']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['button_to_continue']);
        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");

        $this->assertFalse($this->isTextPresent('Срочно жду бюджет логистики'));

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['close']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['popup_unsave']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['close']);

        $this->write_email();
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);


        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=(//*[contains(text(),'Срочно жду бюджет логистики')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['add_recipient']);
        $this->mouseOver("//ul[contains(@class,'ui-autocomplete')]/li[15]/a");
        $this->optimal_click("//ul[contains(@class,'ui-autocomplete')]/li[15]/a");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['del_recipient']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['button_to_continue']);
        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");

        $this->assertFalse($this->isTextPresent('Срочно жду бюджет логистики'));
    }

    private function checkThemes()
    {
        $this->assertTextPresent("Срочно жду бюджет логистики");
        $this->assertTextPresent("Квартальный план");
        $this->assertTextPresent("форма по задаче от логистики - ГОТОВО");
        $this->assertTextPresent("Задача отдела логистики: уточнения");
        $this->assertTextPresent("Обсуждение сроков отчетности");
        $this->assertTextPresent("Беседа с консультантами");
        $this->assertTextPresent("Прочее");
    }
}


/**
 * @}
 */