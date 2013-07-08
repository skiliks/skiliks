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

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        // проверка наличия попапа Входа
        $this->optimal_click("xpath=//*[@id='yw1']/li[2]/a"); // кликаем на кнопку по xpath

        $this->assertTextPresent('Запомнить меня'); // проверяем, что есть особый текст
        $this->optimal_click("css=.submit>input");
        $this->assertTextPresent('Введите логин');
        $this->assertTextPresent('Введите пароль');

        $this->type("xpath=//*[@id='YumUserLogin_username']","asdskiliks.com");
        $this->type("xpath=//*[@id='YumUserLogin_password']","123123");
        $this->optimal_click("css=.submit>input");
        $this->assertTextPresent('Email введён неверно');

        $this->type("xpath=//*[@id='YumUserLogin_username']","selenium@skiliks.com");
        $this->type("xpath=//*[@id='YumUserLogin_password']","not correct password");
        $this->optimal_click("css=.submit>input");
        $this->assertTextPresent('Неверный пароль');

        $this->type("xpath=//*[@id='YumUserLogin_username']","selenium111@skiliks.com");
        $this->type("xpath=//*[@id='YumUserLogin_password']","123123");
        $this->optimal_click("css=.submit>input");
        $this->assertTextPresent('Неверный логин');

        $this->type("xpath=//*[@id='YumUserLogin_username']","selenium@skiliks.com");
        $this->type("xpath=//*[@id='YumUserLogin_password']","111");
        $this->optimal_click("css=.submit>input");
        $this->assertTextPresent('Неверный пароль');

        $this->type("xpath=//*[@id='YumUserLogin_username']","gty1991@gmail.com");
        $this->type("xpath=//*[@id='YumUserLogin_password']","123123");
        $this->optimal_click("css=.submit>input");
        $this->assertTextPresent('E-mail уже зарегистрирован, но не активирован.');

        $this->close();
    }
}