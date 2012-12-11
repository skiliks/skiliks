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
        $session->open($this->browser_url . '/site.php');
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
        sleep(1);
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.drawNewLetter();"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterReceiverBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addReceiver(2)"]', 20)->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterThemeBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addTheme(214)"]')->click();
        $phrase = $this->waitForElement($session, 'css selector', '.mailEmulatorPhrase_369');
        $session->moveto($phrase);
        $session->buttondown();
        $session->moveto($session->element('id', 'mailEmulatorNewLetterText'));
        $session->buttonup();
        $session->element('xpath', '//a[@onclick="mailEmulator.sendNewLetter()"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.drawNewLetter();"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterReceiverBox')->click();
        sleep(2);
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
        sleep(2);

        //close mail
        $this->waitForElement($session, 'xpath', '//button[@onclick="mailEmulator.draw();"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//input[@value="SIM стоп"]')->click();
        sleep(5);
        $simulations = $this->user->simulations();
        $simulation = $simulations[0];
        $our_rows = $this->getWindowLog($simulations);
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

    private function getWindowLog($simulations, $skip_time = true)
    {
        $source = file_get_contents($this->browser_url . '/api/index.php/admin/log?type=Windows');
        $data = CJSON::decode($source);
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
            #$this->assertNotEquals("00:00:00", $row['end_time']);
            if ($skip_time) {
                unset($row['start']);
                unset($row['end']);
                unset($row['start_time']);
                unset($row['end_time']);
            }
            if ($row['window'] == 'main screen' &&
                $row['sub_window'] == 'main screen'
            )
                continue;
            array_push($our_rows, $row);

        }
        return $our_rows;
    }

    /**
     * @large
     */
    public function testTalkLogging()
    {

        # Login
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url . '/site.php');
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
        $this->waitForElement($session, 'xpath', '//a[@id="icons_email"]');#->click();

        $this->setTime($session, "10", "57");
        $this->waitForElement($session, 'css selector', 'li.phone.icon-active', 20);
        $session->element ('xpath', '//a[@id="icons_phone"]')->click();
        $this->waitForElement($session, "xpath", "//a[text()=\"Не ПРИНЯТЬ\"]")->click();
        $this->waitForElement($session, 'css selector', 'li.phone.icon-active', 35);
        $session->element ('xpath', '//a[@id="icons_phone"]')->click();
        $this->waitForElement($session, "xpath", "//a[text()=\"ПРИНЯТЬ\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Раиса Романовна, ну что вы так волнуетесь?! Я уже несколько дней только бюджетом и занимаюсь, до отпуска точно успею.\"]")->click();
        $this->waitForElement($session, 'css selector', 'li.mail.icon-active', 20);
        $this->waitForElement($session, 'xpath', '//a[@id="icons_email"]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="!проблема с сервером!"]')->click();
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.messageReply();"]')->click();
        $session->moveto($this->waitForElement($session, 'xpath', '//span[text()="вашего содействия в получении  "]'));
        $session->buttondown();
        $session->moveto($session->element('id', 'mailEmulatorNewLetterText'));
        $session->buttonup();
        sleep(5);
        $session->element('xpath', '//button[@onclick="mailEmulator.askForSaveDraftLetter();"]')->click();
        $this->waitForElement($session, 'xpath', '//div[@onclick="mailEmulator.doResultForSaveDraftLetter(2);"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.saveDraftLetter()"]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Исходящие "]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Черновики "]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="Re:!проблема с сервером!"]')->click();
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.sendDraftLetter()"]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Исходящие "]')->click();
        $this->waitForElement($session, 'xpath', '//button[@onclick="mailEmulator.draw();"]')->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Ну, с помощью Крутько я должен управиться в эти сроки.\"]")->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//input[@value="SIM стоп"]')->click();
        sleep(5);
        $simulations = $this->user->simulations();
        $simulation = $simulations[0];
        $our_rows = $this->getWindowLog($simulations, false);
        print_r($our_rows);
        for ($i = 1; $i < count($our_rows); $i++) {
            $this->assertFalse($our_rows[$i-1]['window'] == $our_rows[$i]['window']
                && $our_rows[$i-1]['sub_window'] == $our_rows[$i]['sub_window']
                && $our_rows[$i-1]['end_time'] == $our_rows[$i]['end_time']);
        }
    }

    protected function setTime($session, $hour, $minute)
    {
        $session->element('id', 'newTimeH')->value(array("value" => str_split($hour)));
        $session->element('id', 'newTimeM')->value(array("value" => str_split($minute)));
        $session->element('xpath', '//input[@value="Задать"]')->click();
    }
}

