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
class PopupInMailSK1655_Test extends SeleniumTestHelper
{
    public function test_PopupInMail_SK1655() {
        //$this->markTestIncomplete();
        $this->start_simulation("PopupInMailSK1655_Test");

        $this->clearEventQueueBeforeEleven('RST1');

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click("xpath=//*[@id='mlTitle']/tbody/tr[2]/td[2]");
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['plan']);
        $this->checkPopup('Это письмо уже запланировано.');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        $this->checkPopup('Добавьте адресата письма.');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['save']);
        $this->checkPopup('Добавьте адресата письма.');

        $this->addRecipient("xpath=(//*[contains(text(),'Трудякин')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        $this->checkPopup('Укажите тему письма.');
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['save']);
        $this->checkPopup('Укажите тему письма.');

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['popup_cancel']);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['popup_unsave']);
        $this->simulation_stop();
    }

    protected function checkPopup ($str)
    {
        $this->waitForVisible('css=p.mail-popup-text');
        $this->assertText('css=p.mail-popup-text', $str);
        $this->click('css=div.mail-popup-button > div');
    }
}