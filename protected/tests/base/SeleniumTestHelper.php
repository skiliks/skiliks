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
        for ($second = 0; true; $second++)
        {
            if ($second >= 900) {
                $this->assertTrue(false, $message);
            } try {
                if ($this->isVisible($locator))
                {
                    break;
                }
            } catch (Exception $e) { }
            usleep(100000);
        }
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
        $this->waitingLongMethod(
            "!!! FAIL: simulation does not start, because there isn't desktop at the screen!!!",
            "css=.btn.btn-simulation-stop"
        );

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
     * reply_call - method for answering the phone call, when the phone is active (it's moving on)
     */
    protected function reply_call ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
    }

    /**
     * no_reply_call - method for not answering the phone call, when the phone is active (it's moving on)
     */
    protected function no_reply_call ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
    }

    /**
     * write_mail_active - method for writing the new email, when the mail is active (it's moving on)
     */
    protected function write_mail_active()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['mail']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
    }

    /**
     * write_mail_unidentified - method for writing the new email, when we don't know about mail-client status
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
     * optimal_click - method for correct waiting and clicking at the element
     * @loc - locator of the element on which we want to click
     */
    protected function optimal_click ($loc)
    {
        try
        {
            sleep(1);
            $this->waitForVisible($loc);
            $this->click($loc);
            sleep(1);
        }
        catch (Exception $e)
        {
            $this->fail("!!! FAIL: not found ". $loc);
        }
    }

    /**
     * how_much_time - method for determine the game time.
     * return int [] , the first element of the array - is number of hours, second - number of minutes
     */
    protected function how_much_time ()
    {
        $time[0] = (int)($this->getText(Yii::app()->params['test_mappings']['time']['hour']));
        $time[1] = (int)($this->getText(Yii::app()->params['test_mappings']['time']['minute']));
        return $time;
    }

    /**
     * transfer_time - method for changing time
     * @differ - number of minutes in which we need to make transfer
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
     * verify_flag - method for verifing the value of flag
     * @num_flag - number of flag
     * @ver_value - value of flag
     */
    protected function verify_flag ($num_flag, $ver_value)
    {
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

    /**
     * метод для начала написания письма из чистой симуляции
     */
    protected function write_email ()
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click("xpath=(//*[contains(text(),'новое письмо')])");
    }

    /**
     * метод добавления получателя к письму
     * @param $address - locator of the recipient
     */
    protected function addRecipient ($address)
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['add_recipient']);
        sleep(2);
        $this->waitForVisible($address);
        $this->mouseOver($address);
        $this->optimal_click($address);
        sleep(2);
    }

    /**
     * addTheme - method for adding theme to new email
     * @theme - name of theme
     */
    protected function addTheme($theme)
    {
        $this->waitForVisible("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click("xpath=//*[@id='MailClient_NewLetterSubject']/div/a");
        $this->click($theme);
    }

    /**
     * addAttach - method for adding attachment to new email
     * @filename - name of attachment
     */
    protected function addAttach($filename)
    {
        $this->click("xpath=//*[@id='MailClient_NewLetterAttachment']/div/div/a");
        $this->waitForVisible("xpath=(//*[contains(text(), '$filename')])");
        $this->mouseOver("xpath=(//*[contains(text(), '$filename')])");
        $this->click("xpath=(//*[contains(text(), '$filename')])");
    }

    /**
     * метод для очистки не нужных событий из очереди событий
     * параметром нужно написать начальный event, например RST1
     *
     * @event - name of event (e.g. RST1 and RST1.1)
     */
    protected function clearEventQueueBeforeEleven($event)
    {
        $this->run_event($event, Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $event .= '.1';
        $this->run_event($event, Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        $this->logTestResult("delete from event queue ". $event. "\n", true, $this->invite_id);
    }

    /**
     * checkSimPoints - method for checking points of the positive and negative behaviours
     * @positive - value of the positive
     * @negative - value of the negative
     */
    protected function checkSimPoints ($positive,$negative)
    {
        $this->assertText(Yii::app()->params['test_mappings']['log']['admm_positive'],"$positive");
        $this->assertText(Yii::app()->params['test_mappings']['log']['admm_negative'],"$negative");
    }

    /**
     * check_all_urls - method for checking the url, text and link of all buttons for not registered user
     * @all_buttons - array of all
     * @text - value of the negative
     */
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

    /**
     * getInviteId - method for reading invite id from dev panel in dev mode simulation
     */
    protected function getInviteId()
    {
        return $this->getText('id=invite-id');
    }

    /**
     * logTestResult - method for write log of tests in DB
     * @text - text to write in log of test
     * @isFailed - value with the result of the simulation ( was it failed od not)
     * @invite_id - invite id of current simulation
     */
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

    /**
     * getActivationKeyByEmail - method for reading the email activation key for registration from DB
     * @emailU - user email wich was used for registration
     */
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

    /**
     * setUserDetails - method for generating account details for registration
     * @account_type - value (0 or 1) of account type. 0 - personal and 1 - corporate
     */
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

    /**
     * getInviteLink - method for getting the invite link from DB
     * @emailU - email of invited person
     */
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

    /**
     * clear_blocked_auth_users - method for unblock user authorization. It's useful for site tests with password test-cases
     */
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
 
