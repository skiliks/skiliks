<?php
class LoggingTest extends SeleniumTestCase
{
    /**
     * @large
     */
    public function testMailLogging()
    {

        # Login
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url);
        # раскрыть окно на весь экран
        $session->window()->maximize();
        # из-за черной полосы загрузки, пришлось добавить временное ожидание
        sleep(2);
        # вводится текст
        $this->waitForElement($session, "id", "login")->value(
            array("value" => str_split("kaaaaav@gmail.com"))
        );
        # Ждём появления елемента и кликаем на него
        $this->waitForElement($session, "id", "pass")->value(array("value" => str_split("111")));
        # Кликаем на него
        $session->element("xpath", "//input[@class='btn']")->click();
        # Enter Developer Mode - дождаться кнопки, кликнуть на кнопку
        $this->waitForElement($session, 'xpath', "//input[@value='Начать симуляцию developer']");
        $session->element("xpath", "//input[@value='Начать симуляцию developer']")->click();
        # one letter
        $this->waitForElement($session, 'xpath', '//a[@id="icons_email"]')->click();
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.drawNewLetter();"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterReceiverBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addReceiver(2)"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterThemeBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addTheme(214)"]')->click();
        $phrase = $this->waitForElement($session, 'css selector', '.mailEmulatorPhrase_369');
        $session->moveto($phrase);
        $session->buttondown();
        $session->moveto($session->element('id', 'mailEmulatorNewLetterText'));
        $session->buttonup();
        $session->element('xpath', '//a[@onclick="mailEmulator.sendNewLetter()"]')->click();
        sleep(2);
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.drawNewLetter();"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterReceiverBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addReceiver(2)"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterThemeBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addTheme(214)"]')->click();
        $phrase = $this->waitForElement($session, 'css selector', '.mailEmulatorPhrase_369');
        $session->moveto($phrase);
        $session->buttondown();
        $session->moveto($session->element('id', 'mailEmulatorNewLetterText'));
        $session->buttonup();
        $session->element('xpath', '//button[@onclick="mailEmulator.askForSaveDraftLetter();"]')->click();
        $this->waitForElement($session, 'xpath', '//div[@onclick="mailEmulator.doResultForSaveDraftLetter(1);"]')->click();

        //close mail
        $this->waitForElement($session, 'xpath', '//button[@onclick="mailEmulator.draw();"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//input[@value="SIM стоп"]')->click();
        sleep(5);
        $source = file_get_contents($this->browser_url . '/api/index.php/admin/log?type=Windows');
        $data = CJSON::decode($source);
        $simulations = $this->user->simulations();
        $simulation = $simulations[0];
        $our_rows = array();
        $keys = array_keys($data['data']);
        sort($keys);
        foreach ($keys as $key) {
            $row = $data['data'][$key];
            if ($row['id'] != $simulations[0]->primaryKey)
                continue;
            print_r($row);
            $this->assertNotEquals("01.01.1970 00:00:00", $row['start']);
            $this->assertNotEquals("01.01.1970 00:00:00", $row['end']);
            $this->assertNotEquals("00:00:00", $row['start_time']);
            $this->assertNotEquals("00:00:00", $row['end_time']);
            unset($row['start']);
            unset($row['end']);
            unset($row['start_time']);
            unset($row['end_time']);
            if ($row['window'] == 'main screen' &&
                $row['sub_window'] == 'main screen')
                continue;
            array_push($our_rows, $row);

        }
        $this->assertEquals(array(
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => 'kaaaaav@gmail.com',
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail main',
            ),
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => 'kaaaaav@gmail.com',
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail new',
            ),
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => 'kaaaaav@gmail.com',
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail main',
            ),
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => 'kaaaaav@gmail.com',
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail new',
            ),
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => 'kaaaaav@gmail.com',
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail main',
            )
        ), $our_rows);
    }
}

