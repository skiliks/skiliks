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

    public function testZoho() {

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->optimal_click("css=.sign-in-link");
        $this->waitForVisible("css=.login>input");
        $this->type("css=.login>input", "tatiana@skiliks.com");
        $this->type("css=.password>input", "123123");
        $this->optimal_click("css=.submit>input");
        for ($second = 0; ; $second++) {
            if ($second >= 2400) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=(//*[contains(text(),'')])")) break;
            } catch (Exception $e) {}
            usleep(100000);
        }
        $this->createCookie("intro_is_watched_2=yes", "path=/, expires=365");
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
        //кликаем по "Начать" в туториале. Если туториала нет  - значит зохо не загрузился
        $this->click("xpath=(//*[contains(text(),'Начать')])");
    }
}