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
class ChangeMailThemes_SK1253_Test extends SeleniumTestHelper
{
    public function test_ChangeMailThemes_SK1253()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->write_email();
        $this->addRecipient("xpath=(//*[contains(text(),'Трудякин')])");

        //проверяем темы
        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        sleep(2);
        $this->checkThemes();

        $this->addRecipient("xpath=(//*[contains(text(),'Крутько')])");

        //проверяем темы еще раз
        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        sleep(2);
        $this->checkThemes();

        $this->addTheme("xpath=(//*[contains(text(),'Срочно жду бюджет логистики')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['del_recipient']);
        sleep(2);
        $this->addTheme("xpath=(//*[contains(text(),'Отчет по 3 кварталу')])");

        $this->assertFalse($this->isTextPresent('Срочно жду бюджет логистики'));

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['popup_unsave']);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);

        $this->write_email();
        $this->addRecipient("xpath=(//*[contains(text(),'Трудякин')])");
        sleep(2);
        $this->addTheme("xpath=(//*[contains(text(),'Беседа с консультантами')])");

        $this->addRecipient("xpath=(//*[contains(text(),'Крутько')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['del_recipient']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['button_to_continue']);
        sleep(2);
        $this->addTheme("xpath=(//*[contains(text(),'Отчет по 3 кварталу')])");

        $this->assertFalse($this->isTextPresent('Срочно жду бюджет логистики'));
        $this->simulation_stop();
    }

    private function checkThemes()
    {
        $this->assertTextPresent("Срочно жду бюджет логистики");
        $this->assertTextPresent("Беседа с консультантами");
        $this->assertTextPresent("Новая тема");
    }
}