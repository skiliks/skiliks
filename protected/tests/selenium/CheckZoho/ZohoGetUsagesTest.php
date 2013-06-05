<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки процентов использования поточного аккаунта Зохо и даты, до которого можно использовать ключ
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
        //$this->markTestIncomplete();
        $this->setUp();
        $this->deleteAllVisibleCookies();
        //$this->windowMaximize();
        $this->open('https://zapi.zoho.com/login.do');

        $this->selectFrame('zohoiam');

        $this->type('id=lid','zosksk');
        $this->type('name=pwd','zoho531');

        $this->click("name=submit");

        sleep(5);

//        $this->selectFrame("relative=parent");
//
//        sleep(1);

        // $value = split ("%", $this->getText("xpath=//div[2]/div/div/div/table/tbody/tr/td[2]"), 3);
        //$date = $this->getText("xpath=//*[@class='payment_div']/ul/li[1]/span/span");
        //$date = $this->getText("xpath=//div[1]/div[2]/div[1]/div[3]/div[1]/div/ul/li[2]/span");
//
        //$this->click("css=#UserAccount_usg");
        //sleep(5);
//

//        $usages_today = $this->getText("xpath=//div[@class='usage_report_chart']/ul/li[7]/span[1]");
//
//        $date = str_replace(['-',' ', ','],['_','_','_'],$date);
//
//        $this->open('http://test.skiliks.com/cheat/zoho/saveUsageValue/'.urlencode($usages_today).'/'.urlencode($date));


    }
}