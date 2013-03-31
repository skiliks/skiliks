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
 * 2) Повторно звоним Трутневу по этой же теме.
 * 3) Ловим поп-ап, мол, вы уже говорили.
 */
class SK1367_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1367()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('ET1.1',"css=li.icon-active.phone a",'click');
        //$this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        sleep(1);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->optimal_click("css=li.phone_get_history > p");
        $this->optimal_click("//ul[@class='phone-contact-list history']//a[@class='phone_call_back phone-call-btn']");
        $this->optimal_click("xpath=(//*[contains(text(),'Сейчас же приступаю к доработке')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь')])");
        sleep(1);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['missed_calls']);
        $this->optimal_click("//ul[@class='phone-contact-list history']//a[@class='phone_call_back phone-call-btn']");
        $this->waitForVisible('css=p.mail-popup-text');
        $this->assertText('css=p.mail-popup-text','Вы уже обсудили этот вопрос!');
        $this->click('css=div.mail-popup-button > div');

        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['trutnev'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[6]");
        $this->optimal_click("xpath=(//*[contains(text(),'Я по поводу задания от логистов')])");
        $this->optimal_click("xpath=(//*[contains(text(),'двух часов тебе хватит')])");
        sleep(1);
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['trutnev'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[6]");
        $this->waitForVisible('css=p.mail-popup-text');
        $this->assertText('css=p.mail-popup-text','Вы уже обсудили этот вопрос!');
        $this->click('css=div.mail-popup-button > div');
    }
}