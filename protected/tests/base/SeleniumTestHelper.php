<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
* Framework for Selenium-test for site and simulation needs
*/
class SeleniumTestHelper extends CWebTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/var/www/screenshots/'; // path dor screenshots at test.skiliks.com
    protected $screenshotUrl = 'http://screenshots.dev.skiliks.com';
    public $invite_id;
    public $wasCrashed; // attribute about test result

    public static $browsers = array(
        array(
            'name' => 'Firefox',
            'browser' => '*firefox',
            'host' => 'localhost',
            'port' => 4444,
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
     * waitingLongMethod - method for explicit waiting for the element or page was loaded.
     * Is using for waiting for the start of the simulation and for the stop of the simulation
     * @param $message - the message for logging if some element wasn't found
     * @param $locator - the element wat test if looking for
     * @return int
     */
    protected function waitingLongMethod($message, $locator)
    {
        for ($second = 0; true; $second++) {
            if ($second >= 900) {
                $this->fail($message);
            }
            try {
                if ($this->isVisible($locator)) {
                    break;
                }
                return $second;
            } catch (Exception $e) { }
            return $second;
            usleep(100000);
        }

        return $second;
    }

    protected function start_simulation($testName, $user=0)
    {
        $this->setUp();
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        if ($user==0)
        {
            $this->createCookie("cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds=dsiucskcmnxkcjzhxciaowi2039ru948fysuhfiefds8v7sd8djkedbjsaicu9", "path=/, expires=365");
        }
        else
        {
            $this->createCookie("cook_dev_ejbfksbfeksfesfbefbjbbooisnsddsjkfsfnesgjsf=adeshflewfvgiu3428dfgfgdgfg32fgdfgghfgh34e324rfqvf4g534hg54gh5", "path=/, expires=365");
        }

        // short url for start dev mode simulation
        $this->open('/cheat/quick-start/full');

        //waiting for loading all images, css and js and waiting for dev panel is visible below the simulation desktop
        for ($second = 0; ; $second++) {
            if ($second >= 600) $this->fail("!!! FAIL: simulation does not start, because there isn't desktop at the screen!!!");
            try {
                if ($this->isVisible("css=.btn.btn-simulation-stop")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }

        // short js code for closing all alerts
        $this->getEval('var window = this.browserbot.getUserWindow(); window.$(window).off("beforeunload")');

        // logging the start of the simulation
        $this->invite_id = $this->getInviteId();
        $this->logTestResult("start ". $testName. "\n", true, $this->invite_id);

        //clear all queue of events at simulation
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['clear_queue']);
    }

    /**
     * simulation_stop -  method for stop the standard full simulation without checking the results, after deleting results of the simulation if it was successful
     */
    protected function simulation_stop()
    {
        $this->optimal_click("css=.btn.btn-simulation-stop");
        $inv_id = $this->invite_id;
        $this->logTestResult("simStop. Test is successful\n", false, $inv_id);
        $this->simulation_delete(Yii::app()->params['deleteSeleniumResults']);
    }

    /**
     * simulation_stop_demo - method for stop demo simulation
     */
    protected function simulation_stop_demo()
    {
        $this->optimal_click("css=.btn.btn-simulation-stop");
    }

    /**
     * simulation_showLogs - method to stop simulation and check logs of the simulation
     */
    protected function simulation_showLogs()
    {
        $inv_id = $this->invite_id;

        // stop the simulation and wait for popup of the ending of the simulation
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);

        for ($second = 0; ; $second++) {
            if ($second >= 900) $this->fail("!!! FAIL: not found button 'Go' to the results!!!");
            try {
                if ($this->isVisible("css=.mail-popup-button")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }
        $this->optimal_click("css=.mail-popup-button");

        // waiting the dev page with logs and score of the simulation
        for ($second = 0; ; $second++) {
            if ($second >= 900) $this->fail("!!! FAIL: not found button 'universal log' at the page!!!");
            try {
                if ($this->isVisible("id=universal-log")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }
        $this->waitForVisible("id=simulation-points");

        //logging
        $this->logTestResult("simStop and showLogs. Test is successful\n", false, $inv_id);

        //deleting the results of this simulation
        $this->simulation_delete(Yii::app()->params['deleteSeleniumResults']);
    }

    /**
     * simulation_delete - method which can delete the results of the simulation if in config is true
     * @$deleteSuccessfulSimulation - test result attribute
     */
    protected function simulation_delete($deleteSuccessfulSimulation)
    {
        $inv_id = $this->invite_id;

        if ($deleteSuccessfulSimulation === true)
        {
            if ($this->wasCrashed===false)
            {
                /* @var Invite $invite */
                /* @var Simulation $simulation */
                $invite = Invite::model()->findByPk($inv_id);
                $email = $invite->email;
                $sim_for_delete = $invite->simulation_id;
                $simulation = Simulation::model()->findByPk($sim_for_delete);
                SimulationService::removeSimulationData(YumProfile::model()->findByAttributes(['email' => strtolower($email)])->user,
                    $simulation, $sim_for_delete);
            }
        }
    }

    /**
     * run_event - method for start event with its event_code
     * @event - the string with the name of event (from scenario)
     * @next_event - the string with the locator of element which became visible after event starts
     * @after - activity which we need to do with the locator after event was started. F.e. click on the phone icon when we started the event ET1.1
     */
    protected function run_event($event, $next_event="xpath=(//*[contains(text(),'октября')])", $after='-')
    {
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "$event");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);

        for ($second = 0; ; $second++) {
            if ($second >= 600) $this->fail("!!! FAIL: not found ". $next_event. "in the simulation!!!");
            try{
                if ($this->isVisible($next_event))
                {
                    // switch чтобы была возможность расширить дополнительными действиями (кроме клика), а default - если никакие действия не нужны// switch чтобы была возможность расширить дополнительными действиями (кроме клика), а default - если никакие действия не нужны
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
        $this->logTestResult("run event ". $event ." \n", true, $this->invite_id);
    }

    /**
     * call_phone - method for call when the phone icon isn't active
     * @whom - contact name
     * @theme - theme for calling
     */
    protected function call_phone ($whom, $theme)
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
    protected function reply_call ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
    }

    /**
     * no_reply_call - это метод для игнора входящего звонка, когда телефон активен (иконка движется).
     */
    protected function no_reply_call ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
    }

    /**
     * write_mail_active - это метод для создания письма, когда мейл-клиент активен (иконка мигает).
     */
    protected function write_mail_active()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['mail']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
    }

    /**
     * write_mail_unidentified - это метод для создания письма, когда непонятно по ситуации в каком виде находится почтовик.
     */
    protected function write_mail_unidentified()
    {
        if ($this->isElementPresent(Yii::app()->params['test_mappings']['icons_active']['mail'])==true)
        {
            $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['mail']);
            sleep(5);
            if ($this->isVisible(Yii::app()->params['test_mappings']['mail']['to_whom'])==true)
            {
                $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
                $this->optimal_click(Yii::app()->params['test_mappings']['mail']['popup_unsave']);
                $this->optimal_click("css=.NEW_EMAIL");
            }
            else
            {
                $this->optimal_click("css=.NEW_EMAIL");
            }
        }
        else
        {
            $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
            sleep(5);
            if ($this->isVisible(Yii::app()->params['test_mappings']['mail']['to_whom'])==true)
            {
                $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
                $this->optimal_click(Yii::app()->params['test_mappings']['mail']['popup_unsave']);
            }
            else
            {
                $this->optimal_click("css=.NEW_EMAIL");
            }
        }
        $this->logTestResult("write email when mail icon status is unidentified\n", true, $this->invite_id);
    }

    /**
     * optimal_click - это метод для корректного нажатия на элемент (ожидание элемента и только потом нажатие).
     */
    protected function optimal_click ($loc)
    {
        sleep (1);
        try
        {
            $this->waitForVisible($loc);
            $this->click($loc);
        }
        catch (Exception $e)
        {
            $this->fail("!!! FAIL: not found ". $loc);
        }
        sleep (1);
    }

    /**
     * how_much_time - это метод для определения поточного игрового времени.
     * Метод возвращает массив, где первый элемент - это поточное количество часов, а второй элемент -
     * поточное количество минут.
     * Пример использования - метод transfer_time (см. ниже)
     */
    protected function how_much_time ()
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
    protected function transfer_time ($differ)
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
    protected function verify_flag ($num_flag, $ver_value)
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
    protected function incoming_counter ($count)
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
    protected function write_email ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click("xpath=(//*[contains(text(),'новое письмо')])");
    }

    // метод добавления получателя к письму   
    protected function addRecipient ($address)
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['add_recipient']);
        sleep(2);
        $this->waitForVisible($address);
        $this->mouseOver($address);
        $this->optimal_click($address);
        sleep(2);
    }

    // метод добавления темы к письму   
    protected function addTheme($theme)
    {
        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click($theme);
    }

    // метод добавления атача к письму   
    protected function addAttach($filename)
    {
        $this->click("xpath=//*[@id='MailClient_NewLetterAttachment']/div/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(), '$filename')])");
        $this->mouseOver("xpath=(//*[contains(text(), '$filename')])");
        $this->click("xpath=(//*[contains(text(), '$filename')])");
    }

    // метод для очистки не нужных событий из очереди событий
    // параметром нужно написать начальный event, например RST1  
    protected function clearEventQueueBeforeEleven($event)
    {
        $this->run_event($event, Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $event .= '.1';
        $this->run_event($event, Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $this->logTestResult("delete from event queue ". $event. "\n", true, $this->invite_id);
    }

    //*****************************************************
    // БЛОК ДЛЯ РАБОТЫ С ЛОГАМИ ПОСЛЕ ОКОНЧАНИЯ СИМУЛЯЦИИ
    // пример работы с всевозможными логами можно посмотреть в Others/Logging_Case_SK1278_Test.php
    // особенно, если выдает ошибку офсета - нужно смотреть пример в этом файле (строки 61-78)
    //*****************************************************

    // для проверки целосности логов в таблице Universal
    protected function Universal ($array_of_values, $size_of_array)
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
    protected function Mail_log ($array_of_values, $size_of_array)
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
    protected function Leg_actions_detail()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['log']['leg_actions_detail']);
        $this->assertTrue($this->time_values("xpath=//div[2]/table[10]/tbody/tr[", "]/td[2]", "xpath=//div[2]/table[10]/tbody/tr[", "]/td[1]" ));
    }

    // для проверки целосности логов в таблице Leg_actions_aggregated
    protected function Leg_actions_aggregated()
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

    protected function checkSimPoints ($positive,$negative)
    {
        $this->assertText(Yii::app()->params['test_mappings']['log']['admm_positive'],"$positive");
        $this->assertText(Yii::app()->params['test_mappings']['log']['admm_negative'],"$negative");
    }

    // для проверки оценок по Целям обучения (личностные характеристики - пока выпилили - переделываем)
    protected function checkLearningArea($personal10,$personal11,$personal12,$personal13,$personal14,$personal15,$personal16)
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
    protected function check_all_urls ($all_buttons, $text)   // для перехода по всем юрл по циклу
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

    protected function getInviteId()
    {
        return $this->getText('id=invite-id');
    }

    protected function logTestResult ($text='test_text', $isFailed=true, $invite_id)
    {
        try {
            /* @var Invite $invite */
            $this->wasCrashed=$isFailed;
            $invite = Invite::model()->findByPk($invite_id);
            $invite->stacktrace .= $text;
            $invite->is_crashed = $isFailed;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function getActivationKeyByEmail ($emailU)
    {
        /* @var YumProfile $profile */
        $profile = YumProfile::model()->find('email=:email', array(':email'=>$emailU));
        $idUser = $profile->user_id;
        /* @var @var YumUser $yumUser */
        $yumUser = YumUser::model()->findByPk($idUser);
        $key = $yumUser->activationKey;
        $email = strtolower($yumUser->profile->email);
        return "/registration/registration/activation/key/". $key. "/email/". $email;
    }

    protected function setUserDetails ($account_type)
    {
        //$account_type = 0 - personal
        //$account_type = 1 - corporate
        $name = "testName";
        $name .=  (string)rand(100, 300)+(string)rand(20,50)-(string)rand(10,30);
        $surname = "testSurname";
        $surname .= (string)rand(100, 300)+(string)rand(20,50)-(string)rand(10,30);
        $new_email = "test+";
        $new_email .= (string)rand(1, 10000)+(string)rand(1,500)+(string)rand(1,200);
        if ($account_type==0)
        {
            $new_email .= "@pers";
        }
        else
        {
            $new_email .= "@corp";
        }
        $new_email .= (string)rand(1, 500)+(string)rand(1,50)+(string)rand(1,10);
        $new_email .= ".skil.com";
        $UserDetails = array($name,$surname,$new_email,'skiliks123123');
        return $UserDetails;
    }

    protected function getInviteLink ($emailU)
    {
        /* @var Invite $invite */
        $criteria=new CDbCriteria;
        $criteria->condition= 'email=:email';
        $criteria->order= 't.id DESC';
        $criteria->limit= 1;
        $criteria->params= array(':email'=>$emailU);
        $invite = Invite::model()->find($criteria);
        $key = $invite->code;
        return "/registration/by-link/". $key;
    }


    protected function clear_blocked_auth_users()
    {
        $this->open('/ru');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'tetyana.grybok@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'skiliks123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);

        $this->open('/admin_area/users_managament/blocked-authorization-list');

        while ($this->isElementPresent("xpath=//div[2]/div/div[2]/table/tbody/tr[2]/td[1]/a"))
        {
            $this->open('/admin_area/users_managament/blocked-authorization-list');
            $this->optimal_click("xpath=//div[2]/div/div[2]/table/tbody/tr[2]/td[1]/a");
            $this->waitForVisible(Yii::app()->params['test_admin_mappings']['corporate_info']['change_password']);
            $this->optimal_click(Yii::app()->params['test_admin_mappings']['corporate_info']['auth_block']);
        }
        $this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['logout']);
    }
}
 
