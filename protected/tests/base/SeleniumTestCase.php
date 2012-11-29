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

    public function waitForElement($session, $using, $value)
    {
        $timeouts = new PHPWebDriver_WebDriverWait($session, 10, 0.5, array($using, $value));
        return $timeouts->until(function ($session, $args) {
            try {
                return $session->element($args[0], $args[1]);
            } catch (\WebDriver\Exception $e) {
                return false;
            }
        }, 10, 1, array($session, $using, $value));
    }
}
