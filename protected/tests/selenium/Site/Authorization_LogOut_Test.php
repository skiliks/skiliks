<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы попапа Вход (4й пункт тест-плана по сайту) (для SK3222)
 */
class Authorization_LogOut_SK3222_Test extends SeleniumTestHelper
{
    /**
     * test_Authorization_LogOut_SK3222() тестирует задачу SKILIKS-3222 попап Вход
     */
    public function test_Authorization_LogOut_SK3222()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        // проверка наличия попапа Входа
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);

        $this->waitForTextPresent('Запомнить меня'); // проверяем, что есть особый текст
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent('Введите логин');
        $this->assertTextPresent('Введите пароль');

        $this->type(Yii::app()->params['test_mappings']['site']['username'],"asdskiliks.com");
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent('Email введён неверно');

        $this->type(Yii::app()->params['test_mappings']['site']['username'],"selenium.engine@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],"not correct password");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent('Неверный пароль');

        $this->type(Yii::app()->params['test_mappings']['site']['username'],"selenium111@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent('Неверный логин');

        $this->type(Yii::app()->params['test_mappings']['site']['username'],"selenium.engine@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],"111");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent('Неверный пароль');

        $this->type(Yii::app()->params['test_mappings']['site']['username'],"emailForBaned@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent('Аккаунт заблокирован'); //неправильный текст - потом поменять

        $this->type(Yii::app()->params['test_mappings']['site']['username'],"emailNotActivated@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent('E-mail уже зарегистрирован, но не активирован');
    }

    /**
     * test_UserAuth_Authorization_SK5187() тестирует задачу SKILIKS-5187 (страница /user/auth)
     */
    public function test_UserAuth_Authorization_SK5187()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/user/auth');

        $this->waitForVisible(Yii::app()->params['test_mappings']['user_auth']['email']);
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent('Введите логин (email)');
        $this->assertTextPresent('Введите пароль');

        $this->type(Yii::app()->params['test_mappings']['user_auth']['email'],"asdskiliks.com");
        $this->type(Yii::app()->params['test_mappings']['user_auth']['password'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent('Email введён неверно');

        $this->type(Yii::app()->params['test_mappings']['user_auth']['email'],"selenium.engine@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['user_auth']['password'],"not correct password");
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent('Неверный пароль');

        $this->type(Yii::app()->params['test_mappings']['user_auth']['email'],"selenium111@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['user_auth']['password'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent('Неверный логин');

        $this->type(Yii::app()->params['test_mappings']['user_auth']['email'],"selenium.engine@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['user_auth']['password'],"111");
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent('Неверный пароль');

        $this->type(Yii::app()->params['test_mappings']['user_auth']['email'],"emailForBaned@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['user_auth']['password'],"111111");
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent('заблокирован');  //неправильный текст - потом поменять

        $this->type(Yii::app()->params['test_mappings']['user_auth']['email'],"emailNotActivated@skiliks.com");
        $this->type(Yii::app()->params['test_mappings']['user_auth']['password'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent('E-mail уже зарегистрирован, но не активирован');
    }
}