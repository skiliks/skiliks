<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 3/4/13
 * Time: 9:48 PM
 * To change this template use File | Settings | File Templates.
 */
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на тестирование флага F3 (для SK1338, SK1339, SK1340, SK1341, SK1411)
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

        $this->run_event('E1.2');

        $this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'— Закончила? Теперь слушай сюда.')])");

        $krutko=Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->write_mail_active();
        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");

        $this->assertTrue($this->verify_flag('F3','1'));
        print("\n verify_flag");

        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);

        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");

        $this->assertTrue($this->is_it_done("xpath=(//a[contains(text(),'Марина, ну как у')])"));
        print("\n is_it_done\n");
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

        $this->run_event('E1.2');

        $this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Пусть спрашивает')])");

        $krutko=Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->write_mail_active();
        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");

        $this->assertTrue($this->verify_flag('F3','1'));
        print("\n verify_flag");

        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);

        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");

        $this->assertTrue($this->is_it_done("xpath=(//a[contains(text(),'Марина, ну как у')])"));
        print("\n is_it_done\n");
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

        $this->run_event('E1.2');

        $this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ты же у нас такая талантливая и умная!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты будешь выполнять только одну задачу')])");

        $krutko=Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->write_mail_active();
        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");

        $this->assertTrue($this->verify_flag('F3','1'));
        print("\n verify_flag");

        $this->optimal_click(Yii::app()->params['test_mappings']['set_time']['13h']);

        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");

        $this->assertTrue($this->is_it_done("xpath=(//a[contains(text(),'Марина, ну как у')])"));
        print("\n is_it_done\n");
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

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click("xpath=//*[contains(text(),'новое письмо')]");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);

        $this->waitForVisible($krutko);
        $this->mouseOver($krutko);
        $this->click($krutko);

        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->click("xpath=//*[@id='undefined']/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->mouseOver("xpath=(//*[contains(text(),'Сводный бюджет')])");
        $this->click("xpath=(//*[contains(text(),'Сводный бюджет_02_v23')])");
        $this->waitForVisible("xpath=(//a[contains(text(),'отправить')])");
        $this->click("xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])");


        $this->assertFalse($this->verify_flag('F3','1'));
        print("\n verify_flag");

        $this->click("xpath=(//*[contains(text(),'13:00')])");

        $this->call_phone(Yii::app()->params['test_mappings']['phone_contacts']['krutko'], "xpath=//div[@id='phoneCallThemesDiv']/ul/li[2]");

        $this->assertFalse($this->is_it_done("xpath=(//a[contains(text(),'Марина, ну как у')])"));
        print("\n is_it_done\n");
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

        $this->run_event('E1.2');
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, есть срочная работа.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'— Закончила? Теперь слушай сюда.')])");

        $this->waitForVisible("xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[2]/tbody/tr/td[5]");

        $this->assertTrue($this->verify_flag('F3','1'));

        $this->run_event('E2');
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно, Валерий Семенович!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, прямо сейчас проконтролирую, как идет подготовка')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, пожалуйста, вышли прямо сейчас все, что есть по презентации для Босса')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Вот это да! Ладно, отложи пока сводный бюджет и займись презентаций')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что это с тобой случилось?! Столько агрессии')])");
    }
}

