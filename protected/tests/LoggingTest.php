<?php
class LoggingTest extends CWebTestCase
{

    /**
     * @large
     */
    public function testMailLogging()
    {
        $this->markTestIncomplete();

        # Login
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
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.drawNewLetter();"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterReceiverBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addReceiver(2)"]', 20)->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterThemeBox')->click();
        $this->waitForElement($session, 'xpath', '//li[text()="Жалоба"]')->click();
        sleep(10);
        $phrase = $this->waitForElement($session, 'xpath', '//*[@id="mailEmulatorNewLetterTextVariants"]//a/span[text()="внес коррективы"]');
        $session->moveto($phrase);
        $session->buttondown();
        $session->moveto($session->element('id', 'mailEmulatorNewLetterText'));
        $session->buttonup();
        $session->element('xpath', '//a[@onclick="mailEmulator.sendNewLetter()"]')->click();
        sleep(10);
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.drawNewLetter();"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterReceiverBox')->click();
        sleep(2);
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addReceiver(2)"]')->click();
        $this->waitForElement($session, 'css selector', '#mailEmulatorNewLetterThemeBox')->click();
        $this->waitForElement($session, 'xpath', '//li[@onclick="mailEmulator.addTheme(214)"]')->click();
        $phrase = $this->waitForElement($session, 'xpath', '//*[@id="mailEmulatorNewLetterTextVariants"]//a/span[text()="внес коррективы"]');
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
                'email' => $this->email,
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail main',
            ),
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => $this->email,
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail new',
            ),
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => $this->email,
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail main',
            ),
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => $this->email,
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail new',
            ),
            array
            (
                'user_id' => $this->user->primaryKey,
                'email' => $this->email,
                'id' => $simulation->primaryKey,
                'window' => 'mail',
                'sub_window' => 'mail main',
            )
        ), $our_rows);

    }

    public function testDialogLogging()
    {
        # Login
        $this->markTestIncomplete();
        $session = $this->webdriver->session('firefox');
        $session->open($this->browser_url . 'site/');
        # раскрыть окно на весь экран
        $session->window()->maximize();
        # из-за черной полосы загрузки, пришлось добавить временное ожидание
        sleep(3);
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
        # ожидание одного из компонентов (чтобы убедиться что симуляция стартовала)
        $this->waitForElement($session, 'xpath', '//a[@id="icons_documents"]');


        #старт теста по параметрам заданным Антоном

        #добавление события
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E1")));
        $this->waitForElement($session, "id", "addTriggerDelay")->value(array("value" => str_split("1")));
        #клик на "создать событие"
        $session->element("xpath", "//input[@value='Создать']")->click();
        #ждем исчезновения черной полосы загрузки и в появившемся окне тригера кликаем ОК
        sleep(3);
        $session->element('css selector', '.alert a.btn')->click();
        sleep(10);

        #2 ответа на диалог с Денежной

        $this->waitForElement($session, 'xpath', "//p[text()=\"- Раиса Романовна, ну что вы так волнуетесь?! Я уже несколько дней только бюджетом и занимаюсь, до отпуска точно успею.\"]", 30)->click();
        sleep(10);
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Хорошо, за три часа управлюсь.\"]")->click();
        sleep(6);


        #обнуляем поле ввода названия события, вписываем новое, кликаем создать
        $session->element("id", "addTriggerSelect")->clear();
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E8.3")));
        $session->element("xpath", "//input[@value='Создать']")->click();
        #подтвеждение тригера, вейт почему то не работает
        sleep(3);
        $session->element('css selector', '.alert a.btn')->click();
        sleep(6);


        #3 ответа на диалог с начальником АйТи отдела за обедом
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Нет, прости, Мирон. Сегодня просто сумасшедший день. Собирался почту разбирать как только со срочными вопросами разберусь. А им конца и края нет.\"]")->click();
        sleep(25);
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Семен, а у тебя наверняка в бюджете статейка есть на непредвиденные расходы. Это ведь форс-мажор?\"]")->click();
        sleep(6);
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Хорошо, сейчас вернусь и напишу служебку. Спасибо за информацию!\"]")->click();

        sleep(10);


        #обнуляем поле ввода названия события, вписываем новое, кликаем создать
        $session->element("id", "addTriggerSelect")->clear();
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split("E12.1")));
        $session->element("xpath", "//input[@value='Создать']")->click();
        #подтвеждение тригера, вейт почему то не работает
        sleep(3);
        $session->element('css selector', '.alert a.btn')->click();
        sleep(10);


        #3 ответа на диалог
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Может мой аналитик подойти вместо меня?\"]")->click();
        sleep(6);
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Хорошо, буду в 18.00\"]")->click();


        sleep(8);
        #проверка конечного результата
        $this->assertEquals("Сумма оценок: 4.5", $session->element("css selector", ".result-total")->text());
        $this->assertEquals("Сумма оценок 6x: 9", $session->element("css selector", ".result-total-6x")->text());
        $this->assertEquals("Сумма оценок Negative: 0", $session->element("css selector", ".result-total-negative")->text());
        $source = file_get_contents($this->browser_url . '/api/index.php/Admin/Log?data=json&type=Dialogs');
        $json = CJSON::decode($source);
        $data = $json['data'];
        $simulations = $this->user->simulations();
        $simulation = $simulations[0];
        $data = array_filter($data, function ($i) use ($simulation) {return $i['sim_id'] == $simulation->id;});
        $data = array_values($data);
        $data = array_map(function($item){
            if ('00:00:00' !== $item['start_time']) {
                unset($item['start_time']);
            }
            if ('00:00:00' !== $item['end_time']) {
                unset($item['end_time']);
            }
            unset($item['last_id']);
            return $item;
        }, $data);
        $this->assertEquals(array (
            array (
                'sim_id' => $simulation->id,
                'code' => 'E1',
                'category' => 'Разговор по телефону',
                'type_of_init' => 'System_dial'

            ),
            array (
                'sim_id' => $simulation->id,
                'code' => 'E8.3',
                'category' => 'Встреча',
                'type_of_init' => 'System_dial'

            ),
            array (
                'sim_id' => $simulation->id,
                'code' => 'E12.1',
                'category' => 'Разговор по телефону',
                'type_of_init' => 'System_dial'
            ),
        ),$data);
    }

    private function getWindowLog($simulations, $skip_time = true)
    {
        $source = file_get_contents($this->browser_url . '/admin/log?type=Windows');
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

        $this->markTestIncomplete();

        # Login
        $session = $this->webdriver->session('firefox');
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
        # one letter
        $this->waitForElement($session, 'xpath', '//a[@id="icons_email"]'); #->click();

        $this->setTime($session, "10", "57");
        $this->waitForElement($session, 'css selector', 'li.phone.icon-active', 20);
        $session->element('xpath', '//a[@id="icons_phone"]')->click();
        $this->waitForElement($session, "xpath", "//a[text()=\"Не ПРИНЯТЬ\"]")->click();
        $this->waitForElement($session, 'css selector', 'li.phone.icon-active', 35);
        $session->element('xpath', '//a[@id="icons_phone"]')->click();
        $this->waitForElement($session, "xpath", "//a[text()=\"ПРИНЯТЬ\"]")->click();
        $this->waitForElement($session, 'xpath', "//p[text()=\"- Раиса Романовна, ну что вы так волнуетесь?! Я уже несколько дней только бюджетом и занимаюсь, до отпуска точно успею.\"]")->click();
        $this->waitForElement($session, 'css selector', 'li.mail.icon-active', 20);
        $this->waitForElement($session, 'xpath', '//a[@id="icons_email"]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="!проблема с сервером!"]')->click();
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.messageReply();"]')->click();
        $session->buttondown(array(
            'element' => $this->waitForElement($session, 'xpath', '//span[text()="вашего содействия в получении"]')->getID()
        ));
        sleep(5);
        $session->buttonup(array('element' => $session->element('id', 'mailEmulatorNewLetterText')));
        sleep(5);
        $session->element('xpath', '//button[@onclick="mailEmulator.askForSaveDraftLetter();"]')->click();
        $this->waitForElement($session, 'xpath', '//div[@onclick="mailEmulator.doResultForSaveDraftLetter(2);"]')->click();
        sleep(5);
        $this->waitForElement($session, 'xpath', '//a[@onclick="mailEmulator.saveDraftLetter()"]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Исходящие "]')->click();
        $this->waitForElement($session, 'xpath', '//a[text()="Черновики "]')->click();
        $this->waitForElement($session, 'xpath', '//td[text()="re: !проблема с сервером!"]')->click();
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
        for ($i = 1; $i < count($our_rows); $i++) {
            $this->assertFalse($our_rows[$i - 1]['window'] == $our_rows[$i]['window']
                && $our_rows[$i - 1]['sub_window'] == $our_rows[$i]['sub_window']
                && $our_rows[$i - 1]['end_time'] == $our_rows[$i]['end_time']);
        }
    }

    protected function setTime($session, $hour, $minute)
    {
        $session->element('id', 'newTimeH')->value(array("value" => str_split($hour)));
        $session->element('id', 'newTimeM')->value(array("value" => str_split($minute)));
        $session->element('xpath', '//input[@value="Задать"]')->click();
    }
}

