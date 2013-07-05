<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 4/23/13
 * Time: 12:32 PM
 * To change this template use File | Settings | File Templates.
 */
class Authorization_LogOut_SK3222_Test extends SeleniumTestHelper
{
    /**
     * test_Authorization_LogOut_SK3222() тестирует задачу SKILIKS-3222.
     */
    public function test_Authorization_LogOut_SK3222() {
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

        // проверка наличия попапа Входа
        $this->optimal_click("xpath=//*[@id='yw1']/li[2]/a"); // кликаем на кнопку по xpath

        $this->assertTextPresent('Запомнить меня'); // проверяем, что есть особый текст
        $this->optimal_click("css=.submit>input");
        $this->assertTextPresent('Введите логин');
        $this->assertTextPresent('Введите пароль');

        $this->close();
    }