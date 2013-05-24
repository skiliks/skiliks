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
        $buttons_xpath = array ("xpath=//*[@id='yw0']/li[1]/a", "xpath=//*[@id='yw0']/li[2]/a", "xpath=//*[@id='yw0']/li[3]/a", "xpath=//*[@id='yw2']/li[1]/a", "xpath=//*[@id='yw2']/li[2]/a", "xpath=//*[@id='yw2']/li[3]/a", "xpath=//*[@id='yw1']/li[2]/a");
        $all_buttons = array($buttons_xpath, $buttons_text, $text_inside);

        $buttons_text_en = array('Home','About Us','Product','Home', 'About Us', 'Product');
        $text_inside_en= array('easiest','Meet','About the Product', 'easiest','Meet','About the Product');
        $buttons_xpath_en = array ("xpath=//*[@id='yw0']/li[1]/a", "xpath=//*[@id='yw0']/li[2]/a", "xpath=//*[@id='yw0']/li[3]/a", "xpath=//*[@id='yw2']/li[1]/a", "xpath=//*[@id='yw2']/li[2]/a", "xpath=//*[@id='yw2']/li[3]/a", "xpath=//*[@id='yw1']/li[2]/a");
        $all_buttons_en = array($buttons_xpath_en, $buttons_text_en, $text_inside_en);

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->check_all_urls($all_buttons, "English");

        // проверка наличия попапа Входа
        $this->optimal_click($all_buttons[0][sizeof($all_buttons[0])-1]); // кликаем на кнопку по xpath
        sleep(5);
        $this->isTextPresent($all_buttons[2][sizeof($all_buttons[0])-1]); // проверяем, что есть особый текст

        $this->optimal_click("css=.ui-icon.ui-icon-closethick");

        // проверка Тарифов
        $this->optimal_click("xpath=//*[@id='yw0']/li[4]/a"); // кликаем на кнопку по xpath
        sleep(5);
        $this->isTextPresent("Тарифные"); // проверяем, что есть особый текст
        for ($j = 0; $j<sizeof($all_buttons[0]) ; $j++)  // цикл проверки есть ли все нужные кнопки
        {
            $this->isTextPresent($all_buttons[1][$j]); // проверяем, что у этих кнопок правильный текст
        }

        // проверка английской версии
        $this->optimal_click("xpath=//*[@id='yw0']/li[1]/a");
        $this->optimal_click("xpath=//*[@id='yw1']/li[1]/a");

        $this->check_all_urls($all_buttons_en, "Русский");

        $this->optimal_click("xpath=//*[@id='yw0']/li[1]/a");
        sleep(5);
        $this->optimal_click("xpath=//*[@id='subscribe-form']/div[2]/input");
        sleep(1);
        $this->isTextPresent("Невалидный email - ''!");

        $new_email = "gty1991+";
        $new_email .= (string)rand(1, 10000);
        $new_email .= "@bigmir.net";

        $this->type("xpath=//*[@id='user-email-value']", $new_email);
        $this->optimal_click("xpath=//*[@id='subscribe-form']/div[2]/input");
        sleep(10);
        $this->isTextPresent("Thank");

        $this->optimal_click("xpath=//*[@id='yw0']/li[1]/a");
        sleep(5);
        $this->type("xpath=//*[@id='user-email-value']", $new_email);
        $this->optimal_click("xpath=//*[@id='subscribe-form']/div[2]/input");
        sleep(1);
        $this->isTextPresent("уже был добавлен!");

    }
}