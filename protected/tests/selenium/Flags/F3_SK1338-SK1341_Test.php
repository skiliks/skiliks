<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для флага F3 (для SK1338, SK1339, SK1340, SK1341, SK1411)
 */
class F3_SK1338_1341_SK1411_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * testSK1338() тестирует задачу SKILIKS-1338 для dialog
     *
     * 1. Запускаем E1.2
     * 2. Кликаем по диалогу до фразы "— Закончила? Теперь слушай сюда."
     * 3. Пишем письмо к Крутько с темой "Сводный бюджет: файл" с вложением "Сводный бюджет_02_v23" и отправляем
     * 4. Проверяем, что флаг F3 поменялся
     * 5. Устанавливаем время на 13:00
     * 6. Звоним к Крутько по теме "Сводный бюджет: контроль"
     * 7. Проверяем, что диалог запускается
     * 8. Заканчиваем симуляцию
     */
    public function testSK1338() {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('E1.2', "xpath=(//*[contains(text(),'Марина, есть срочная работа.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'— Закончила? Теперь слушай сюда.')])");

        $this->miracle_send_email ();
        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);
        $this->assertTrue($this->verify_flag('F3','1'));
        sleep(3);
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[3]");
        $this->assertTrue($this->is_it_done("xpath=(//a[contains(text(),'Марина, ну как у')])"));
        $this->click("css=input.btn.btn-simulation-stop");
    }

    /**
     * testSK1339() тестирует задачу SKILIKS-1339 для dialog
     *
     * 1. Запускаем E1.2
     * 2. Кликаем по диалогу до фразы "...Пусть спрашивает..."
     * 3. Пишем письмо к Крутько с темой "Сводный бюджет: файл" с вложением "Сводный бюджет_02_v23" и отправляем
     * 4. Проверяем, что флаг F3 поменялся
     * 5. Устанавливаем время на 13:00
     * 6. Звоним к Крутько по теме "Сводный бюджет: контроль"
     * 7. Проверяем, что диалог запускается
     * 8. Заканчиваем симуляцию
     */
    public function testSK1339() {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];
        $this->run_event('E1.2', "xpath=(//*[contains(text(),'Марина, есть срочная работа.')])", 'click');
        //$this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Пусть спрашивает')])");

        $this->miracle_send_email ();
        $this->assertTrue($this->verify_flag('F3','1'));
        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);
        sleep(3);
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[3]");

        $this->assertTrue($this->is_it_done("xpath=(//a[contains(text(),'Марина, ну как у')])"));
        $this->click("css=input.btn.btn-simulation-stop");
    }

    /**
     * testSK1340() тестирует задачу SKILIKS-1340 для dialog
     *
     * 1. Запускаем E1.2
     * 2. Кликаем по диалогу до фразы "...А ты будешь выполнять только одну задачу..."
     * 3. Пишем письмо к Крутько с темой "Сводный бюджет: файл" с вложением "Сводный бюджет_02_v23" и отправляем
     * 4. Проверяем, что флаг F3 поменялся
     * 5. Устанавливаем время на 13:00
     * 6. Звоним к Крутько по теме "Сводный бюджет: контроль"
     * 7. Проверяем, что диалог запускается
     * 8. Заканчиваем симуляцию
     */
    public function testSK1340() {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];
        $this->run_event('E1.2', "xpath=(//*[contains(text(),'Марина, есть срочная работа.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ты же у нас такая талантливая и умная!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты будешь выполнять только одну задачу')])");

        $this->miracle_send_email ();
        $this->assertTrue($this->verify_flag('F3','1'));
        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);
        sleep(3);
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[3]");
        $this->assertTrue($this->is_it_done("xpath=(//a[contains(text(),'Марина, ну как у')])"));
        $this->click("css=input.btn.btn-simulation-stop");
    }

    /**
     * testSK1341() тестирует задачу SKILIKS-1341 для dialog
     *
     * 1. Пишем письмо к Крутько с темой "Сводный бюджет: файл" с вложением "Сводный бюджет_02_v23" и отправляем
     * 2. Проверяем, что флаг F3 не поменялся (F3=0)
     * 3. Устанавливаем время на 13:00
     * 4. Звоним к Крутько по теме "Сводный бюджет: контроль"
     * 5. Проверяем, что диалог с фразой "...Марина, ну как у..." не запускается
     * 6. Заканчиваем симуляцию
     */
    public function testSK1341() {
        $this->start_simulation();

        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->write_email();

        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->optimal_click($krutko);

        $this->optimal_click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->optimal_click("xpath=(//*[contains(text(),'Сводный бюджет: файл')])");

        $this->addAttach('Сводный бюджет_02_v23');
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[contains(text(),'13:00')])");
        $this->assertFalse($this->verify_flag('F3','1'));

        sleep(5);
        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[3]");
        sleep(5);
        $this->assertFalse($this->is_it_done("xpath=(//a[contains(text(),'Марина, ну как у')])"));
        $this->click("css=input.btn.btn-simulation-stop");
    }

    /**
     * testSK1411() тестирует задачу SKILIKS-1411 для replica
     *
     * 1. Запускаем E1.2
     * 2. Кликаем по диалогу до фразы "— Закончила? Теперь слушай сюда..."
     * 3. Проверяем, что флаг F3 поменялся
     * 5. Запускаем E2
     * 6. Кликаем по диалогу до фразы "Что это с тобой случилось?! Столько агрессии..."
     */
    public function testSK1411()
    {
        $this->start_simulation();
        $this->run_event('E1.2', "xpath=(//*[contains(text(),'Марина, есть срочная работа.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'— Закончила? Теперь слушай сюда.')])");
        sleep(5);
        $this->assertTrue($this->verify_flag('F3','1'));

        $this->run_event('E2', "xpath=(//*[contains(text(),'Конечно, Валерий Семенович!')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, прямо сейчас проконтролирую, как идет подготовка')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, пожалуйста, вышли прямо сейчас все, что есть по презентации для Босса')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Вот это да! Ладно, отложи пока сводный бюджет и займись презентаций')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что это с тобой случилось?! Столько агрессии')])");
    }
}

