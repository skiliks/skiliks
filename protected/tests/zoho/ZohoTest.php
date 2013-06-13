<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Cтартуем фулл промо симуляцию, ждем загрузку зохо
 */
class ZohoTest extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public static $browsers = array(
        array(
            'name'    => 'Firefox',
            'browser' => '*firefox',
            'host'    => 'localhost',
            'port'    => 4444,
            'timeout' => 30000,
        )
    );
    public function testZoho() {

        $this->setUp();
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('http://new.skiliks.com/ru');
        $this->createCookie("cook_dev_ladskasdasddaxczxpoicuwcnzmcnzdewedjbkscuds=dsiucskcmnxkcjzhxciaowi2039ru948fysuhfiefds8v7sd8djkedbjsaicu9", "path=/, expires=365");
        $this->open('http://new.skiliks.com/cheat/quick-start/full');

        $this->optimal_click("xpath=(//*[contains(text(),'Load docs')])");
        $this->getEval('var window = this.browserbot.getUserWindow(); window.$(window).off("beforeunload")');
        sleep(5);


        $startTimer = microtime(true);
        $dateOfStart = date("d.m.y");
        $timeOfStart = date("H:i:s");

        for ($second = 0; ; $second++) {
            if ($second >= 12000)
            {
                $message = "Failed";
                break;
            }
            try {
                if (!($this->isTextPresent("Пожалуйста, подождите, идёт загрузка документов")))
                {
                    $message = "Passed";
                    break;
                }
            } catch (Exception $e)
            {
                print ($e);
            }
            usleep(10000);
        }

        $timeDifference = microtime(true) - $startTimer;

        $dateOfEnd = date("d.m.y");
        $timeOfEnd = date("H:i:s");

        if ($timeDifference>120)
        {
            $message = "Failed";
            $this->failed("Failed for timeout");
        }

        /*print(' Date of start= ');
        print ($dateOfStart);
        print(' Time of start = ');
        print ($timeOfStart);
        print(' Date of end = ');
        print ($dateOfEnd);
        print(' Time of end = ');
        print ($timeOfEnd);
        print(' Time of loading documents = ');
        print($timeDifference);
        print(' Message = ');
        print($message);
        print(' ');*/
        $this->close();
    }
}