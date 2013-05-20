<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Cтартуем фулл промо симуляцию, ждем загрузку зохо
 */
class Zoho_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function test_Zoho() {

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->optimal_click("css=.sign-in-link");
        $this->waitForVisible("css=.login>input");
        $this->type("css=.login>input", "tatiana@skiliks.com");
        $this->type("css=.password>input", "123123");
        $this->optimal_click("css=.submit>input");
        for ($second = 0; ; $second++) {
            if ($second >= 600) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=(//*[contains(text(),'')])")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }
        $this->createCookie("intro_is_watched=yes", "path=/, expires=365");
        $this->open('/simulation/promo/full');
        //ждем появление поп-апа "Подождите, идет загрузка документов"
        for ($second = 0; ; $second++) {
            if ($second >= 600) $this->fail("Can't see popup 'Please, wait, documents is loading...'");
            try {
                if ($this->isVisible("xpath=(//*[contains(text(),'Пожалуйста, подождите, идёт загрузка документов')])")) break;
            } catch (Exception $e) {}
            usleep(100000);;
        }
        //ждем самой загрузки документов
        sleep (95);
        //кликаем по "ок" в туториале. Если туториала нет - значит зохо не загрузился
        $this->click("xpath=(//*[contains(text(),'xpath=//*[@id='messageSystemMessageDiv']/div/table/tbody/tr/td/div')])");
    }
}