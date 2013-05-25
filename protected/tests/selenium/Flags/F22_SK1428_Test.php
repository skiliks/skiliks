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

    public function testSK1428()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('T7.3',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Поговорил с Трудякиным.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, отложи все дела и сделай срочно')])");
        sleep(5);
        $this->assertTrue($this->verify_flag('F22','1'));

        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])",'click');
        $this->waitForVisible("xpath=(//*[contains(text(),'Данные у вас в почте. Только что отправил')])");
        $this->optimal_click("css=li.icon-active.mail a");
        sleep(2);
        $this->optimal_click("xpath=//*[@id='mlTitle']/tbody/tr[1]/td[2]");
        sleep(2);
        $this->verifyTextPresent("Вот, сделал. Смотрите. \nС уваженим, Трутнев С.");
        $this->stop();
    }

    public function testSK1428_Case2()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        sleep(5);

        $this->assertTrue($this->verify_flag('F22','0'));

        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])",'click');
        $this->waitForVisible("xpath=(//*[contains(text(),'Данные у вас в почте. Только что отправил')])");

        $this->optimal_click("css=li.icon-active.mail a");
        sleep(2);
        $this->optimal_click("xpath=//*[@id='mlTitle']/tbody/tr[1]/td[2]");
        sleep(2);
        $this->verifyTextPresent("exact:Добрый день! Заполнил вашу форму. Есть ли вопросы? \nВсего доброго, Трутнев С.");
        $this->stop();
    }
}