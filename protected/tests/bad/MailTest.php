<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 19.12.12
 * Time: 18:13
 * To change this template use File | Settings | File Templates.
 */
class MailTest extends SeleniumTestCase
{
    /**
     * @large
     */
    public function testMail()
    {

        $this->markTestIncomplete();
        # Login
        $session = $this->webdriver->session('firefox',
            array("browserName" => "phantomjs",
            "version" => "",
            "platform" => "ANY",
            "javascriptEnabled" => True));
        $session->open($this->browser_url . '/site/');
        # раскрыть окно на весь экран
        $session->window()->maximize();
        # из-за черной полосы загрузки, пришлось добавить временное ожидание
        sleep(2);
        # вводится текст
        $this->waitForElement($session, "id", "login")->value(
            array("value" => str_split($this->email))
        );
        # Ждём появления елемента и кликаем на него
        $this->waitForElement($session, "id", "pass")->value(array("value" => str_split("111")));
        # Кликаем на него
        $session->element("css selector", "input.btn-primary")->click();
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию developer']");
        $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
        $this->markTestIncomplete();
        # one letter
        $this->waitForElement($session, 'xpath', '//a[@id="icons_email"]')->click();
        sleep(20);
        $this->waitForElement($session, 'css selector', 'a.NEW_EMAIL')->click();
        $this->waitForElement($session, 'css selector', '#MailClient_RecipientsList')->click();
        $this->waitForElement($session, 'css selector', '.ui-menu-item a', 20);
        /**
         * @var PHPWebDriver_WebDriverElement[] $elements
         */
        $elements = $session->elements('css selector', '.ui-menu-item a');
        foreach ($elements as $element) {
            if (preg_match('/Денежная/', $element->text())) {
                $element->click();
                break;
            }
        }
        $this->waitForElement($session, 'xpath', '//option[text()="Жалоба"]')->click();
        sleep(10);
        $phrase = $this->waitForElement($session, 'xpath', '//a/span[text()="внес коррективы"]')->click();
        $session->element('css selector', 'a.SEND_EMAIL')->click();
        sleep(10);
        $this->waitForElement($session, 'css selector', 'a.NEW_EMAIL')->click();
        $this->waitForElement($session, 'css selector', '#MailClient_RecipientsList')->click();
        $this->waitForElement($session, 'css selector', '.ui-menu-item a', 20);
        /**
         * @var PHPWebDriver_WebDriverElement[] $elements
         */
        $elements = $session->elements('css selector', '.ui-menu-item a');
        foreach ($elements as $element) {
            if (preg_match('/Денежная/', $element->text())) {
                $element->click();
                break;
            }
        }
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterThemeBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addTheme(214)"]')->click();
        $phrase = $this->waitForElement($session, 'xpath', '//*[@id="mailEmulatorNewLetterTextVariants"]//a/span[text()="внес коррективы"]');
        $session->moveto($phrase);
        $session->buttondown();
        $session->moveto($session->element('id', 'mailEmulatorNewLetterText'));
        $session->buttonup();
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.saveDraftLetter()"]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Исходящие "]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Черновики "]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="Жалоба"]')->click();
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.sendDraftLetter()"]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Исходящие "]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="Жалоба"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//button[@onclick="mailEmulator.draw();"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//input[@value="SIM стоп"]')->click();

    }

    public function testReply()
    {

        # Login
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url . 'site/');
        # раскрыть окно на весь экран
        $session->window()->maximize();
        # из-за черной полосы загрузки, пришлось добавить временное ожидание
        sleep(2);
        # вводится текст
        $this->waitForElement($session, "id", "login")->value(
            array("value" => str_split($this->email))
        );
        # Ждём появления елемента и кликаем на него
        $this->waitForElement($session, "id", "pass")->value(array("value" => str_split("111")));
        # Кликаем на него
        $session->element("css selector", "input.btn-primary")->click();
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию developer']");
        $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
        # one letter
        $this->waitForElement($session, 'xpath', '//a[@id="icons_email"]')->click();
        sleep(1);
        $this->waitForElement($session, 'xpath', '//td[text()="По ценовой политике"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorOpenedMailAnswer a')->click();
        $phrase = $this->waitForElement($session, 'xpath', '//*[@id="mailEmulatorNewLetterTextVariants"]//a/span[text()="внес коррективы"]');
        $session->moveto($phrase);
        $session->buttondown();
        $session->moveto($session->element('id', 'mailEmulatorNewLetterText'));
        $session->buttonup();
        $session->element('xpath', '//a[@onclick="mailEmulator.sendNewLetter()"]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="По ценовой политике"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorOpenedMailAnswer a')->click();
        $phrase = $this->waitForElement($session, 'xpath', '//*[@id="mailEmulatorNewLetterTextVariants"]//a/span[text()="внес коррективы"]');
        $session->moveto($phrase);
        $session->buttondown();
        $session->moveto($session->element('id', 'mailEmulatorNewLetterText'));
        $session->buttonup();
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.saveDraftLetter()"]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Исходящие "]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Черновики "]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="re: По ценовой политике"]')->click();
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.sendDraftLetter()"]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Исходящие "]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="re: По ценовой политике"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//button[@onclick="mailEmulator.draw();"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//input[@value="SIM стоп"]')->click();

    }
}
