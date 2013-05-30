<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Cтартуем фулл промо симуляцию, ждем загрузку зохо
 */
class ZohoGetUsagesTest extends SeleniumTestHelper
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
        $this->open('https://zapi.zoho.com/login.do');

        sleep(2);

        $this->type('id=lid','zosksk');
        $this->type('name=pwd','zoho531');

        $this->optimal_click("name=submit");

        sleep(2);

        $value = $this->getText('.usageinner_topdiv table td[1]');

        $this->open('http://test.skiliks.com/cheat/zoho/saveUsageValue/'.urlencode($value));

        $this->close();
    }
}