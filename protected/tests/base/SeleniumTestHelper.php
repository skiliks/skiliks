<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Класс с методами для Selenium Test(-ов)
 */
class SeleniumTestHelper extends CWebTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/var/www/screenshots/';
    protected $screenshotUrl = 'http://screenshots.dev.skiliks.com';

    public static $browsers = array(
        array(
            'name'    => 'Firefox',
            'browser' => '*firefox',
            'host'    => 'localhost',
            'port'    => 4444,
            'timeout' => 30000,
        )
    );

    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->stop();
    }

    /**
     * start_simulation - это метод, который включает стандартные действия при начале симуляции
     * (начиная с открытия окна браузера до самого входа в dev-режим).
     */
    public function start_simulation()
    {
        $this->setUp();
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->createCookie("cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds=dsiucskcmnxkcjzhxciaowi2039ru948fysuhfiefds8v7sd8djkedbjsaicu9", "path=/, expires=365");
        $this->open('/cheat/quick-start/full');

        for ($second = 0; ; $second++) {
            if ($second >= 600) $this->fail("Timeout. Not found id=addTriggerSelect");
            try {
                if ($this->isVisible("id=addTriggerSelect")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }

        $this->getEval('var window = this.browserbot.getUserWindow(); window.$(window).off("beforeunload")');
    }

    public function simulation_stop()
    {
        $this->optimal_click("css=.btn.btn-simulation-stop");
    }

    public function simulation_showLogs()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        for ($second = 0; ; $second++) {
            if ($second >= 900) $this->fail("Timeout. Not found button Go to the results");
            try {
                if ($this->isVisible("css=.mail-popup-button")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }
        $this->optimal_click("css=.mail-popup-button");
        for ($second = 0; ; $second++) {
            if ($second >= 900) $this->fail("Timeout. Not found id=universal-log");
            try {
                if ($this->isVisible("id=universal-log")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }
        $this->waitForVisible("id=simulation-points");
    }

    /**
     * run_event - это метод для запуска события по его event_code.
     * next_event - это локатор следующего события(звонок телефона или приход письма), которого мы ожидаем и должны что-то с ним сделать после
     * after - если надо что-то с этим локатором сделать после, то сюда пишем click, а если нет - то можно что-то другое написать. Оно расспознает пока только click
     * запустили event = ET1.1 -> next_event = css=li.icon-active.phone a (звонок телефона) -> after = click (мы кликаем по иконке телефона)
     * если еще что-то надо, то можно дописать в switch
     */
    public function run_event($event, $next_event="xpath=(//*[contains(text(),'октября')])", $after='-')
    {
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "$event");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);

        for ($second = 0; ; $second++) {
            if ($second >= 600) $this->fail($next_event);
            try{
                if ($this->isVisible($next_event))
                {
                    // switch чтобы была возможность расширить дополнительными действиями (кроме клика), а default - если никакие действия не нужны
                    switch ($after) {
                        case 'click':
                            {
                                sleep(1);
                                $this->click($next_event);
                                break;
                            }
                        default:
                            break;
                    }
                    break;
                }
            } catch (Exception $e) {}
            usleep(100000);
        }
    }

    /**
     * call_phone - это метод для звонка по телефону, когда телефон не активен (иконка не движется).
     * Где whom - это адресат письма, а theme - тема звонка.
     */
    public function call_phone ($whom, $theme)
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->mouseOver(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->click(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->waitForElementPresent($whom);
        $this->mouseOver($whom);
        $this->click($whom);
        $this->waitForElementPresent($theme);
        $this->mouseOver($theme);
        $this->click($theme);
    }

    /**
     * reply_call - это метод для ответа на входящий звонок, когда телефон активен (иконка движется).
     */
    public function reply_call ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
    }

    /**
     * no_reply_call - это метод для игнора входящего звонка, когда телефон активен (иконка движется).
     */
    public function no_reply_call ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
    }

    /**
     * write_mail_active - это метод для создания письма, когда мейл-клиент активен (иконка мигает).
     */
    public function write_mail_active()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['mail']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
    }

    /**
     * optimal_click - это метод для корректного нажатия на элемент (ожидание элемента и только потом нажатие).
     */
    public function optimal_click ($loc)
    {
        sleep (1);
        $this->waitForVisible($loc);
        $this->click($loc);
        sleep (1);
    }

    /**
     * how_much_time - это метод для определения поточного игрового времени.
     * Метод возвращает массив, где первый элемент - это поточное количество часов, а второй элемент -
     * поточное количество минут.
     * Пример использования - метод transfer_time (см. ниже)
     */
    public function how_much_time ()
    {
        $time[0] = (int)($this->getText(Yii::app()->params['test_mappings']['time']['hour']));
        $time[1] = (int)($this->getText(Yii::app()->params['test_mappings']['time']['minute']));
        return $time;
    }

    /**
     * transfer_time - это метод для переноса времени на differ минут.
     * Метод стоит использовать для коректного изменения времени для выполнения событий,
     * которые должны происходить с задержкой, где differ -это колличество минут задежки.
     */
    public function transfer_time ($differ)
    {
        $time_array=$this->how_much_time(); //запускаем определение текущего времени
        $time_array[1]=$time_array[1]+$differ;  // к минутам приплюсовываем необходимую разницу времени
        if ($time_array[1]>=60) // проверяем выходим ли мы за рамки по минутам
        {                                              // если выходим за рамки 60 минут, то
            $time_array[0]=$time_array[0]+1;  // увеличиваем количество часов на 1
            $time_array[1]=$time_array[1]-60; // изменяем количество минут
        }
        // меняем поточное время
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], $time_array[0]);
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], $time_array[1]);
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        return $time_array;
    }

    /**
     * verify_flag - это метод для проверки, что значение флага num_flag поменялось
     * и соответсвует значению ver_value.
     * Возвращаем true, если поменялось значение флага и
     * возвращаем false, если не изменилось.
     */
    public function verify_flag ($num_flag, $ver_value)
    {
        //sleep(5);
        $was_changed=false;
        $current_value='0';
        for ($second = 0; ; $second++) {

            if ($second >= 600)
            {
                $was_changed = false;
                break;
            }
            else
            {
                $current_value=$this->getText(Yii::app()->params['test_mappings']['flags'][$num_flag]);
                if ($current_value == $ver_value)
                {
                    $was_changed=true;
                    break;
                }
            }
            usleep(100000);
        }
        return $was_changed;
    }

    /**
     * incoming_counter - это метод для проверки, что количество писем = count.
     * count - количество писем, которые мы ожидаем увидеть во "входящих" на момент указанного времени (время устанавливаем перед вызовом этого метода).
     * Возвращаем true, если количество ожидаемых писем и реальных входящих совпадают
     * возвращаем false, если нет
     */
    public function incoming_counter ($count)
    {
        $same_number = false;
        $was_changed = false;
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons_active']['mail']);
        if ($this->isVisible(Yii::app()->params['test_mappings']['icons_active']['mail']))
        {
            for ($second = 0; ; $second++) {
                if ($second >= 600)
                {
                    $was_changed = false;
                    break;
                }
                else
                {
                    if ($this->isVisible(Yii::app()->params['test_mappings']['icons']['mail']))
                    {
                        $was_changed = true;
                        break;
                    }
                }
                usleep(100000);
            }
        }
        else
        {
            $was_changed = true;
        }

        sleep(20);
        $numb_of_incoming = 0;
        if ($was_changed==true)
        {
             $numb_of_incoming = (int)($this->getText("//*[@id='icons_email']/span"));
            if ($numb_of_incoming == $count)
            {
                $same_number=true;
            }
        }

        return $same_number;
    }

    // метод для начала написания письма из чистой симуляции
    public function write_email ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click("xpath=(//*[contains(text(),'новое письмо')])");
    }

    // метод добавления получателя к письму
    public function addRecipient ($address)
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['add_recipient']);
        sleep(2);
        $this->waitForVisible($address);
        $this->mouseOver($address);
        $this->optimal_click($address);
    }

    // метод добавления темы к письму
    public function addTheme($theme)
    {
        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click($theme);
    }

    // метод добавления атача к письму
    public function addAttach($filename)
    {
        $this->click("xpath=//*[@id='MailClient_NewLetterAttachment']/div/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(), '$filename')])");
        $this->mouseOver("xpath=(//*[contains(text(), '$filename')])");
        $this->click("xpath=(//*[contains(text(), '$filename')])");
    }

    // метод для очистки не нужных событий из очереди событий
    // параметром нужно написать начальный event, например RST1
    public function clearEventQueueBeforeEleven($event)
    {
        $this->run_event($event, Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $event .= '.1';
        $this->run_event($event, Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
    }

    //*****************************************************
    // БЛОК ДЛЯ РАБОТЫ С ЛОГАМИ ПОСЛЕ ОКОНЧАНИЯ СИМУЛЯЦИИ
    // пример работы с всевозможными логами можно посмотреть в Others/Logging_Case_SK1278_Test.php
    // особенно, если выдает ошибку офсета - нужно смотреть пример в этом файле (строки 61-78)
    //*****************************************************

    // для проверки целосности логов в таблице Universal
    public function Universal ($array_of_values, $size_of_array)
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['log']['universal']);
        $new_size = $this->size_of_logs("xpath=//div[2]/table[1]/tbody/tr[", "]/td[4]");
        if ($new_size==$size_of_array)
        {
            $a = $this->time_values("xpath=//div[2]/table[1]/tbody/tr[", "]/td[4]", "xpath=//div[2]/table[1]/tbody/tr[", "]/td[3]" );
            $b = $this->active_windows($array_of_values, "xpath=//div[2]/table[1]/tbody/tr[", "]/td[1]", "xpath=//div[2]/table[1]/tbody/tr[", "]/td[2]");
            if (($a==True)&&($b==True))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    // для проверки целосности логов в таблице Mail_log
    public function Mail_log ($array_of_values, $size_of_array)
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['log']['mail_log']);
        $new_size = $this->size_of_logs("xpath=//div[2]/table[7]/tbody/tr[", "]/td[4]");
        if ($new_size==$size_of_array)
        {
            $a = $this->active_windows($array_of_values,"xpath=//div[2]/table[7]/tbody/tr[", "]/td[3]", "xpath=//div[2]/table[7]/tbody/tr[", "]/td[5]" );
            if ($a==True)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    // для проверки целосности логов в таблице Leg_actions_detail
    public function Leg_actions_detail()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['log']['leg_actions_detail']);
        $this->assertTrue($this->time_values("xpath=//div[2]/table[10]/tbody/tr[", "]/td[2]", "xpath=//div[2]/table[10]/tbody/tr[", "]/td[1]" ));
    }

    // для проверки целосности логов в таблице Leg_actions_aggregated
    public function Leg_actions_aggregated()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['log']['leg_actions_aggregated']);
        $this->assertTrue($this->time_values("xpath=//div[2]/table[11]/tbody/tr[", "]/td[9]", "xpath=//div[2]/table[11]/tbody/tr[", "]/td[8]" ));
    }

    // метод для проверки величины лога (определенной таблицы, у которой первая ячейка передается в локаторах 1 и 2)
    protected function size_of_logs ($loc1,$loc2)
    {
        $i = 1;
        while (true)
        {
            $result = "";
            $result .= $loc1;
            $result .= (string)$i;
            $result .= $loc2;
            if ($this->isElementPresent($result)==true)
            {
                $i++;
            }
            else
            {
                break;
            }
        }
        return ($i-1);
    }

    // метод для корректного считывания времени из ячеек и перевода в нужный формат
    protected function time_values ($a1, $b1, $a2, $b2)
    {
        $count = 1;
        $i = 1;
        while (true)
        {
            $result = "";
            $result .= $a1;
            $result .= (string)$count;
            $result .= $b1;

            $result2 = "";
            $result2 .= $a2;
            $result2 .= (string)($count+1);
            $result2 .= $b2;

            if (($this->isElementPresent($result)==true)&&($this->isElementPresent($result2)==true))
            {
                $first_time = trim($this->getText($result));
                $second_time = trim($this->getText($result2));

                $parsed_time_1 = explode(":", $first_time);
                $parsed_time_2 = explode(":", $second_time);

                $time1_in_sec = $parsed_time_1[0]*3600+$parsed_time_1[1]*60+$parsed_time_1[2];
                $time2_in_sec = $parsed_time_2[0]*3600+$parsed_time_2[1]*60+$parsed_time_2[2];

                if (($time2_in_sec>=$time1_in_sec)&&(($time2_in_sec-$time1_in_sec)<=2))
                {
                    $count++;
                }
                else
                {
                    break;
                }
                $i++;
            }
            else
            {
                break;
            }
        }
        return $i==$count;
    }

    // для проверки разницы между началом действия события до его окончания
    protected function difference_of_time ($a1, $b1, $a2, $b2)
    {
        $count = 1;
        $i = 1;
        while (true)
        {
            $result = "";
            $result .= $a1;
            $result .= (string)$count;
            $result .= $b1;

            $result2 = "";
            $result2 .= $a2;
            $result2 .= (string)$count;
            $result2 .= $b2;

            if (($this->isElementPresent($result)==true)&&($this->isElementPresent($result2)==true))
            {
                $first_time = trim($this->getText($result));
                $second_time = trim($this->getText($result2));

                $parsed_time_1 = explode(":", $first_time);
                $parsed_time_2 = explode(":", $second_time);

                $time1_in_sec = $parsed_time_1[0]*3600+$parsed_time_1[1]*60+$parsed_time_1[2];
                $time2_in_sec = $parsed_time_2[0]*3600+$parsed_time_2[1]*60+$parsed_time_2[2];

                $time_differ[$count] = $time2_in_sec - $time1_in_sec;
                $count++;
            }
            else
            {
                break;
            }
        }
        return $time_differ;
    }

    // для проверки 2 колонок в определенной таблице с значениями массива, который передали
    protected function active_windows($array_of_values, $a1, $b1, $a2, $b2 )
    {
        $match = 1;
        $i=1;
        while (true)
        {
            $result = "";
            $result .= $a1;
            $result .= (string)$i;
            $result .= $b1;

            $result2 = "";
            $result2 .= $a2;
            $result2 .= (string)$i;
            $result2 .= $b2;

            if (($this->isElementPresent($result)==true)&&($this->isElementPresent($result2)==true))
            {
                if ($array_of_values[1][($i-1)]==trim($this->getText($result)))
                {
                    if ($array_of_values[0][($i-1)]==trim($this->getText($result2)))
                    {
                        $match++;
                    }
                }
            }
            else
            {
                break;
            }
            $i++;
        }
        return $i==$match;
    }

    //********************************************
    // БЛОК ДЛЯ ПРОВЕРКИ ОЦЕНОК ЗА СИМУЛЯЦИЮ
    //********************************************

    public function checkSimPoints ($positive,$negative)
    {
        $this->assertText(Yii::app()->params['test_mappings']['log']['admm_positive'],"$positive");
        $this->assertText(Yii::app()->params['test_mappings']['log']['admm_negative'],"$negative");
    }

    // для проверки оценок по Целям обучения (личностные характеристики - пока выпилили - переделываем)
    public function checkLearningArea($personal10,$personal11,$personal12,$personal13,$personal14,$personal15,$personal16)
    {
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal10'],"$personal10");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal10'],"$personal10");
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal11'],"$personal11");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal11'],"$personal11");
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal12'],"$personal12");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal12'],"$personal12");
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal13'],"$personal13");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal13'],"$personal13");
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal14'],"$personal14");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal14'],"$personal14");
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal15'],"$personal15");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal15'],"$personal15");
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal16'],"$personal16");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal16'],"$personal16");
    }

    //********************************************
    // БЛОК ДЛЯ ПРОВЕРКИ РАБОТЫ САЙТА
    //********************************************
    public function check_all_urls ($all_buttons, $text)   // для перехода по всем юрл по циклу
    {
        for ($i = 0; $i<sizeof($all_buttons[0])-1 ; $i++) {
            $this->optimal_click($all_buttons[0][$i]); // кликаем на кнопку по xpath
            sleep(5);
            $this->assertTextPresent($all_buttons[2][$i]); // проверяем, что есть особый текст
            $this->assertTextPresent($text);
            for ($j = 0; $j<sizeof($all_buttons[0])-1 ; $j++)  // цикл проверки есть ли все нужные кнопки
            {
                $this->assertTextPresent($all_buttons[1][$j]); // проверяем, что у этих кнопок правильный текст
            }
            sleep(1);
        }
    }
}

