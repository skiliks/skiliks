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

    public function testZohoGetUsages() {
        $this->markTestIncomplete();
        $this->setUp();
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('https://zapi.zoho.com/login.do');

        $this->selectFrame('zohoiam');

        $this->type('id=lid','zosksk');
        $this->type('name=pwd','zoho531');

        $this->click("name=submit");

        // $this->click("//form[@id='login']/table/tr[0]/td[0]/table/tr[7]/td[1]/input");

        sleep(2);

        $value = $this->getText("//div[.usageinner_topdiv']/table/td[1]");

        $this->open('http://test.skiliks.com/cheat/zoho/saveUsageValue/'.urlencode($value));

        $this->close();
    }
}