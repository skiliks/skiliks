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
        $this->start_simulation();
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->waitForVisible("xpath=(//*[contains(text(),'По ценовой политике')])");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['plan']);
        $this->checkPopup('Это письмо уже запланировано.');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        $this->checkPopup('Добавьте адресата письма.');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['save']);
        $this->checkPopup('Добавьте адресата письма.');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        sleep(5);
        $this->waitForVisible(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['send']);
        $this->checkPopup('Укажите тему письма.');
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['save']);
        $this->checkPopup('Укажите тему письма.');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['close']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['popup_cancel']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['close']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['popup_unsave']);
    }

    protected function checkPopup ($str)
    {
        $this->waitForVisible('css=p.mail-popup-text');
        $this->assertText('css=p.mail-popup-text', $str);
        $this->click('css=div.mail-popup-button > div');
    }
}