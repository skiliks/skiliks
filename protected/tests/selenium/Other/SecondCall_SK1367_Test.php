<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Case1
 * 1) Запускаем ET1.1, нажимаем "НЕ ответить"
 * 2) В списке пропущенных звонков выбираем звонок Денежной и нажимаем "Позвонить".
 * 3) Ловим поп-ап, мол, вы уже говорили с ней.
 * Case2
 * 1) Звоним Трутневу (уточнение по задаче от логистов)
 * 2) Проверяем пропала ли тема из телефона, по которой мы уже позвонили
 */
class SecondCall_SK1367_Test extends SeleniumTestHelper
{
    public function test_SecondCall_SK1367()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->optimal_click('link=F32');
        $this->run_event('ET1.1',Yii::app()->params['test_mappings']['icons']['phone'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        sleep(1);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->optimal_click("css=li.phone_get_history > p");
        $this->optimal_click("//ul[@class='phone-contact-list history']//a[@class='phone_call_back phone-call-btn']");
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Сейчас же приступаю к доработке')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь')])");
        sleep(1);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['missed_calls']);
        $this->optimal_click("//ul[@class='phone-contact-list history']//a[@class='phone_call_back phone-call-btn']");
        $this->waitForVisible('css=p.mail-popup-text');
        $this->assertText('css=p.mail-popup-text','Вы уже обсудили этот вопрос!');
        $this->click('css=div.mail-popup-button > div');

        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['trutnev'], "xpath=(//*[contains(text(),'Задача отдела логистики: статус')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я по поводу задания от логистов')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'сейчас поговорю с ним и уточню задание')])");
        sleep(10);

        $this->clearEventQueueBeforeEleven('RST1');
        sleep(2);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->mouseOver(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->click(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['phone_contacts']['trutnev']);
        $this->mouseOver(Yii::app()->params['test_mappings']['phone_contacts']['trutnev']);
        $this->click(Yii::app()->params['test_mappings']['phone_contacts']['trutnev']);
        sleep(2);
        $this->assertFalse($this->isElementPresent("xpath=(//*[contains(text(),'Задача отдела логистики: статус')])"));

        $this->mouseOver("xpath=(//*[contains(text(),'Отчет по 3 кварталу')])");
        $this->click("xpath=(//*[contains(text(),'Отчет по 3 кварталу')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Завершить')])");

        //$this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->mouseOver(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->click(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['phone_contacts']['trutnev']);
        $this->mouseOver(Yii::app()->params['test_mappings']['phone_contacts']['trutnev']);
        $this->click(Yii::app()->params['test_mappings']['phone_contacts']['trutnev']);
        sleep(2);
        $this->assertTrue($this->isElementPresent("xpath=(//*[contains(text(),'Отчет по 3 кварталу')])"));

        $this->simulation_stop();
    }
}