<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест по флагу F22.
 * Сase 1. Запускаем T7.3, требуем от Трутнева прислать на проверку задание от логистов.
 * Убеждаемся что F22=1.
 * Запускаем T7.4, торопим Трутнева. Получаем письмо, проверяем текст (превью)
 *
 * Case 2. После старта симуляции убеждаемся что F22=0.
 * Запускаем T7.4, торопим Трутнева. Получаем письмо, проверяем текст (превью)
 */
class F22_SK1428_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1428()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('T7.3',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Поговорил с Трудякиным.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, отложи все дела и сделай срочно')])");
        sleep(5);
        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[4]");

        $this->assertTrue($this->verify_flag('F22','1'));

        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])",'click');
        $this->waitForVisible("xpath=(//*[contains(text(),'Данные у вас в почте. Только что отправил')])");
        $this->optimal_click("css=li.icon-active.mail a");
        sleep(2);
        $this->verifyTextPresent("Вот, сделал. Смотрите. \nС уваженим, Трутнев С.");
    }

    public function testSK1428_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);
        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[4]");

        $this->assertTrue($this->verify_flag('F22','0'));

        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])",'click');
        $this->waitForVisible("xpath=(//*[contains(text(),'Данные у вас в почте. Только что отправил')])");

        $this->optimal_click("css=li.icon-active.mail a");
        sleep(2);
        $this->verifyTextPresent("exact:Добрый день! Заполнил вашу форму. Есть ли вопросы? \nВсего доброго, Трутнев С.");
    }
}