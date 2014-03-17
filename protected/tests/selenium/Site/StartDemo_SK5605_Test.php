<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для проверки доступности кнопки "начать демо" на всех страницах русской версии сайта
 */
class StartDemo_SK5605_Test extends SeleniumTestHelper
{
    /**
     * test_StartDemo_SK5605() тестирует задачу SKILIKS-5605
     */
    public function test_StartDemo_SK5605()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['home']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['about_us']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['about_product']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['tariffs']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['registration']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['old_registration']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['help']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['404']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['user_auth']);
        $this->open_url(Yii::app()->params['test_mappings']['site_urls']['after_registration']);

        $this->open_en_urls(Yii::app()->params['test_mappings']['site_urls']['ru-en']);
        $this->open_en_urls(Yii::app()->params['test_mappings']['site_urls']['home']);
        $this->open_en_urls(Yii::app()->params['test_mappings']['site_urls']['about_us']);
        $this->open_en_urls(Yii::app()->params['test_mappings']['site_urls']['about_product']);
        $this->open_en_urls(Yii::app()->params['test_mappings']['site_urls']['tariffs']);

    }

    /**
     * @param $url
     */
    public function open_url($url)
    {
        $this->open($url);

        try {
            $this->optimal_click("xpath=(//*[contains(text(),'Начать демо')])");
            $this->optimal_click("xpath=(//*[contains(text(),'Далее')])");

            for ($second = 0; ; $second++) {
                if ($second >= 600) $this->fail("!!! FAIL: demo does not start, because there isn't desktop at the screen!!!");
                try {
                    if ($this->isVisible("css=.manual-toggle.icon")) break;
                } catch (Exception $e) {
                }
                usleep(100000);
            }
        } catch (Exception $e) {
            $this->fail("There is no button for start demo at the url = " . $url);
        }
        $this->getEval('var window = this.browserbot.getUserWindow(); window.$(window).off("beforeunload")');
        $this->open("/");

    }

    /**
     * @param $url
     */
    public function open_en_urls($url)
    {
        $this->open($url);
        $this->waitForVisible("xpath=(//*[contains(text(),'Русский')])");
        try {
            $this->isElementPresent("xpath=(//*[contains(text(),'Начать демо')])");
        } catch (Exception $e) {
            $this->fail("Button for start demo is present at english version at the url = " . $url);
        }
    }
}