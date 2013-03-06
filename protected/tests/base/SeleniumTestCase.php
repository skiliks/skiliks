<?php

require_once('PHPWebDriver/WebDriver.php');
require_once('PHPWebDriver/WebDriverWait.php');

/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 15.11.12
 * Time: 16:52
 * To change this template use File | Settings | File Templates.
 * @property string email
 */

class SeleniumTestCase extends CDbTestCase
{
    /**
     * @var PHPWebDriver_WebDriver
     */
    protected $webdriver;
    protected $browser_url;

    /**
     * @var Users
     */
    protected $users;
    /**
     * @var PHPWebDriver_WebDriverSession session
     */
    protected $session;

    /**
     * @medium
     */
    protected function setUp()
    {
        $this->webdriver = new PHPWebDriver_WebDriver();
        $this->browser_url = Yii::app()->params['frontendUrl'];
        $this->email = 'kaaabv@gmail.com';
        #$this->browser_url = 'http://live.skiliks.com/';
        $this->createInitialUsers();
        parent::setUp();
    }

    protected function tearDown()
    {
        foreach ($this->webdriver->sessions() as $session) {
            try {
                $session->close();
            } catch (PHPWebDriver_UnhandledWebDriverError $e) {

            }
        }
        parent::tearDown();
    }

    protected function waitForElement($session, $using, $value, $timeout = 10)
    {
        $timeouts = new PHPWebDriver_WebDriverWait($session, $timeout, 1, array($using, $value));
        return $timeouts->until(function ($session, $args) {
            try {
                if ($session->element($args[0], $args[1]) && $session->element($args[0], $args[1])->displayed()) {
                    return $session->element($args[0], $args[1]);
                } else {
                    return false;
                };
            } catch (\WebDriver\Exception $e) {
                return false;
            }
        }, 10, 1, array($session, $using, $value));
    }

    public function waitForNoElement($session, $using, $value)
    {
        $timeouts = new PHPWebDriver_WebDriverWait($session, 10, 0.5, array($using, $value));
        return $timeouts->until(function ($session, $args) {
            try {
                return !($session->element($args[0], $args[1]) && $session->element($args[0], $args[1])->displayed());
            } catch (\WebDriver\Exception $e) {
                return true;
            }
        }, 10, 1, array($session, $using, $value));
    }

    protected function startSimulation($session)
    {
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
    }

    private function createInitialUsers()
    {
        foreach (YumUser::model()->findAllByAttributes(array('email' => 'kaaabv@gmail.com')) as $user) {
            $user->delete();
        }
        $user = new YumUser();
        $user->email = $this->email;
        $user->password = md5('111');
        $user->is_active = true;
        $user->save();
        $group = new UserGroup();
        $group->uid = $user->primaryKey;
        $group->gid = 2;
        $group->save();
        $this->user = $user;
    }

    protected function runEvent($session, $event, $delay = 0)
    {
        $this->waitForElement($session, "id", "addTriggerSelect")->value(array("value" => str_split($event)));
        $this->waitForElement($session, "id", "addTriggerDelay")->value(array("value" => str_split($delay)));
        $session->element("xpath", "//input[@value='Создать']")->click();
        sleep(3);
        $session->element('css selector', '.alert a.btn')->click();

    }
}
