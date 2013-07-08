<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 1) Заходим в почтовик, планируем письмо в фокусе. Ловим поп-ап "Это письмо уже запланировано"
 * 2) Отправка/сохранение письмо без темы, ловим поп-апы. Проверяем в них текст
 * 3) Отправляем сохраняем письмо без адресата ловим поп-апы. Проверяем в них текст
 * 4) Закрываем окно написания нового письма, кликаем "Отмена". Снова закрываем окно, кликаем "Не сохранять"
 */
class PunctuationMarks_SK1801_Test extends SeleniumTestHelper
{

    public function test_PunctuationMarks_SK1801()
    {
        $this->start_simulation();
        sleep(2);
        $this->write_email();
        sleep(2);
        $this->addRecipient(Yii::app()->params['test_mappings']['mail_contacts']['krutko']);
        sleep(2);
        $this->addTheme("xpath=(//*[contains(text(),'Отчет по 3 кварталу')])");

        $this->optimal_click("xpath=//*[@id='mailEmulatorNewLetterTextVariantsAdd']/li[1]/a/span");
        $this->click("xpath=//*[@id='mailEmulatorNewLetterTextVariantsAdd']/li[2]/a/span");
        $this->click("xpath=//*[@id='mailEmulatorNewLetterTextVariantsAdd']/li[3]/a/span");
        $this->click("xpath=//*[@id='mailEmulatorNewLetterTextVariantsAdd']/li[4]/a/span");
        $this->click("xpath=//*[@id='mailEmulatorNewLetterTextVariantsAdd']/li[5]/a/span");
        $this->click("xpath=//*[@id='mailEmulatorNewLetterTextVariantsAdd']/li[6]/a/span");

        $this->optimal_click("xpath=(//*[contains(text(),'отправить')])");
        sleep(5);

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['outbox']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отчет для Правления')])");
        $this->assertTrue($this->mail_comes("Отчет по 3 кварталу"));
        $this->optimal_click("xpath=//*[@id='mlTitle']/tbody/tr/td[2]");
        sleep(2);

        $this->assertTrue($this->isTextPresent(". , : \" - ;"));
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);

        $this->close();
    }
}