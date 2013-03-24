<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Запускаем E1.2
 * Выбираем реплику, результатом которой является MS
 * Переходим в почтовик. Убеждаемся что находимся в окне написания нового письма
 * Закрываем почтовик. Запускаем ивент MS70, открываем окно почтовика, убеждаемся что находимся на mail main
 */
class SendAfterMS_SK1369_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1369()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('E1.2',"xpath=(//*[contains(text(),'Марина, есть срочная работа')])",'click');
        //$this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Теперь слушай сюда')])");
        $this->optimal_click("css=li.icon-active.mail a");
        $this->waitForVisible("link=отправить");
        $this->assertElementPresent("link=отправить");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['close']);
        $this->optimal_click("css=div.mail-popup-button > div");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['close']);
        $this->run_event('M70',Yii::app()->params['test_mappings']['icons']['mail'],'click');
        //$this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['mail_main']['new_email']);
        $this->assertElementPresent(Yii::app()->params['test_mappings']['mail_main']['new_email']);
    }
}