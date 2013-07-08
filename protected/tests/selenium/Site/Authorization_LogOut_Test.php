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

        $this->close();
    }
}