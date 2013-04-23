<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 4/23/13
 * Time: 12:32 PM
 * To change this template use File | Settings | File Templates.
 */
class Case_for_no_registered_user_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * test_SK1274_Case() тестирует задачу SKILIKS-1274. Проверка задержки для события, которое было отложено из-за невозможности одновременного запуска
     */
    public function test_for_no_registered_user() {
        //$this->markTestIncomplete();

        $buttons_text = array('Главная','О нас','О продукте','Главная', 'О нас', 'О продукте', 'Вход');
        $text_inside= array('Самый','Познакомьтесь – наша команда','Мы создали онлайн бизнес симуляцию', 'Самый','Познакомьтесь – наша команда','Мы создали онлайн бизнес симуляцию','Запомнить меня');
        $buttons_xpath = array ("xpath=//*[@id='yw0']/li[1]/a", "xpath=//*[@id='yw0']/li[2]/a", "xpath=//*[@id='yw0']/li[3]/a", "xpath=//*[@id='top']/div[4]/footer/nav/a[1]", "xpath=//*[@id='top']/div[4]/footer/nav/a[2]", "xpath=//*[@id='top']/div[4]/footer/nav/a[3]", "xpath=//*[@id='yw1']/li[2]/a");
        $all_buttons = array($buttons_xpath, $buttons_text, $text_inside);

        $buttons_text_en = array('Home','About Us','Product','Home', 'About Us', 'Product');
        $text_inside_en= array('easiest','Meet','About the Product', 'easiest','Meet','About the Product');
        $buttons_xpath_en = array ("xpath=//*[@id='yw0']/li[1]/a", "xpath=//*[@id='yw0']/li[2]/a", "xpath=//*[@id='yw0']/li[3]/a", "xpath=//*[@id='top']/div[4]/footer/nav/a[1]", "xpath=//*[@id='top']/div[4]/footer/nav/a[2]", "xpath=//*[@id='top']/div[4]/footer/nav/a[3]", "xpath=//*[@id='yw1']/li[2]/a");
        $all_buttons_en = array($buttons_xpath_en, $buttons_text_en, $text_inside_en);

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->check_all_urls($all_buttons, $all_buttons_en);

        $this->optimal_click("xpath=//*[@id='yw0']/li[1]/a");
        sleep(5);
        $this->optimal_click("xpath=//*[@id='subscribe-form']/div[2]/input");
        sleep(1);
        $this->isTextPresent("Невалидный email - ''!");

        $this->type("xpath=//*[@id='user-email-value']", "asd@skiliks.com");
        $this->optimal_click("xpath=//*[@id='subscribe-form']/div[2]/input");
        sleep(10);
        $this->isTextPresent("Thank");

        $this->optimal_click("xpath=//*[@id='yw0']/li[1]/a");
        sleep(5);
        $this->type("xpath=//*[@id='user-email-value']", "asd@skiliks.com");
        $this->optimal_click("xpath=//*[@id='subscribe-form']/div[2]/input");
        sleep(1);
        $this->isTextPresent("Адрес - asd@skiliks.com уже был добавлен!");

    }
}