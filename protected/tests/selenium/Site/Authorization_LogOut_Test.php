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

        $this->clear_blocked_auth_users();

        $this->open('/ru');

        // проверка наличия попапа Входа
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);

        $this->waitForTextPresent('Запомнить меня'); // проверяем, что есть особый текст

        //пустые значения
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent('Введите логин');
        $this->assertTextPresent('Введите пароль');

        $this->loginPopup("asdskiliks.com","123123",'Email введён неверно');
        $this->loginPopup("selenium.engine@skiliks.com","not correct password",'Неверный пароль');
        $this->loginPopup("selenium111@skiliks.com","123123",'Неверный логин');
        $this->loginPopup("selenium.engine@skiliks.com","111",'Неверный пароль');
        $this->loginPopup("emailForBaned@skiliks.com","123123",'Аккаунт заблокирован'); //неправильный текст - потом поменять
        $this->loginPopup("emailNotActivated@skiliks.com","123123",'E-mail уже зарегистрирован, но не активирован');
    }

    /**
     * test_UserAuth_Authorization_SK5187() тестирует задачу SKILIKS-5187 (страница /user/auth)
     */
    public function test_UserAuth_Authorization_SK5187()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->clear_blocked_auth_users();

        $this->open('/user/auth');

        $this->waitForVisible(Yii::app()->params['test_mappings']['user_auth']['email']);

        //пустые значения
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent('Введите логин (email)');
        $this->assertTextPresent('Введите пароль');

        $this->loginUserAuth("asdskiliks.com", "123123", 'Email введён неверно' );
        $this->loginUserAuth("selenium.engine@skiliks.com", "not correct password", 'Неверный пароль' );
        $this->loginUserAuth("selenium111@skiliks.com", "123123", 'Неверный логин' );
        $this->loginUserAuth("selenium.engine@skiliks.com", "111", 'Неверный пароль' );
        $this->loginUserAuth("emailForBaned@skiliks.com", "111111", 'заблокирован' ); //неправильный текст - потом поменять
        $this->loginUserAuth("emailNotActivated@skiliks.com", "123123", 'E-mail уже зарегистрирован, но не активирован' );
    }

    public function loginPopup($email, $password, $message)
    {
        $this->type(Yii::app()->params['test_mappings']['site']['username'], $email);
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'], $password);
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForTextPresent($message);
    }

    public function loginUserAuth($email, $password, $message)
    {
        $this->type(Yii::app()->params['test_mappings']['user_auth']['email'], $email);
        $this->type(Yii::app()->params['test_mappings']['user_auth']['password'], $password);
        $this->optimal_click(Yii::app()->params['test_mappings']['user_auth']['login']);
        $this->waitForTextPresent($message);
    }
}