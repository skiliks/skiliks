<?php

require_once('PHPWebDriver/WebDriver.php');
require_once('PHPWebDriver/WebDriverWait.php');

/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 15.11.12
 * Time: 16:52
 * To change this template use File | Settings | File Templates.
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

    public function waitForElement($session, $using, $value, $timeout = 10)
    {
        $timeouts = new PHPWebDriver_WebDriverWait($session, $timeout, 0.5, array($using, $value));
        return $timeouts->until(function ($session, $args) {
            try {
                return $session->element($args[0], $args[1]);
            } catch (\WebDriver\Exception $e) {
                return false;
            }
        }, 10, 0.3, array($session, $using, $value));
    }

    public function waitForNoElement($session, $using, $value)
    {
        $timeouts = new PHPWebDriver_WebDriverWait($session, 10, 0.5, array($using, $value));
        return $timeouts->until(function ($session, $args) {
            try {
                return !$session->element($args[0], $args[1]);
            } catch (\WebDriver\Exception $e) {
                return true;
            }
        }, 10, 1, array($session, $using, $value));
    }

    private function createInitialUsers()
    {
        foreach (Users::model()->findAllByAttributes(array('email' => 'kaaaaav@gmail.com')) as $user) {
            $user->delete();
        }
        $user = new Users();
        $user->email = 'kaaabv@gmail.com';
        $user->password = md5('111');
        $user->is_active = true;
        $user->save();
        $group = new UserGroupsModel();
        $group->uid = $user->primaryKey;
        $group->gid = 2;
        $group->save();
        $this->user = $user;
    }
}
