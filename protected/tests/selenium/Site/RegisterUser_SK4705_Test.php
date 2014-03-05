<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы регистрации персонального и корпоративного аккаунта (для SK4705 и SK4706)
 */
class RegisterUser_SK4705_Test extends SeleniumTestHelper
{
    /**
     * test_RegisterCorporate_SK4705() тестирует задачу SKILIKS-4705.
     */
    public function test_RegisterCorporate_SK4705()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->assertTextPresent('Запомнить меня');
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['registration_link_popup']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logo_img']);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['registration_button']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logo_img']);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['registration_link_header']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);

        //check needed information for this page
        $this->assertTextPresent('Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию');

        //0 - personal
        $account_details = $this->setUserDetails(1);

        // all is empty
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Введите имя');
        $this->assertTextPresent('Введите фамилию');
        $this->assertTextPresent('Введите email');
        $this->assertTextPresent('Введите пароль');
        $this->assertTextPresent('Подтвердите пароль');
        $this->assertTextPresent('Вы должны согласиться с условиями');
        sleep(1);

        $this->type(Yii::app()->params['test_mappings']['site_register']['userName'],$account_details[0]);
        $this->type(Yii::app()->params['test_mappings']['site_register']['userSurname'],$account_details[1]);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);
        $this->type(Yii::app()->params['test_mappings']['site_register']['password1']," ");
        $this->type(Yii::app()->params['test_mappings']['site_register']['password2']," ");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Введите пароль');
        $this->assertTextPresent('Подтвердите пароль');
        sleep(1);

        $this->type(Yii::app()->params['test_mappings']['site_register']['password1'],"123");
        $this->type(Yii::app()->params['test_mappings']['site_register']['password2'],"123");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Не менее 6 символов');
        sleep(1);

        $this->type(Yii::app()->params['test_mappings']['site_register']['password1'],"123123");
        $this->type(Yii::app()->params['test_mappings']['site_register']['password2'],"123123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Пароли не совпадают');
        sleep(1);

        $this->type(Yii::app()->params['test_mappings']['site_register']['password1'],"123123");
        $this->type(Yii::app()->params['test_mappings']['site_register']['password2'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Пароль слишком простой');
        sleep(1);

        $this->type(Yii::app()->params['test_mappings']['site_register']['password1'],$account_details[3]);
        $this->type(Yii::app()->params['test_mappings']['site_register']['password2'],$account_details[3]);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Введите email');
        sleep(1);

        // wrong email
        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], "wrongEmail");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Email введён неверно');
        sleep(1);

        //baned email
        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], "emailForBaned@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Аккаунт emailforbaned@skiliks.com заблокирован');
        $this->assertTextPresent('Данный email занят');
        sleep(1);

        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], "tetyana.grybok@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Данный email занят');
        sleep(1);

        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], "emailNotActivated@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('E-mail уже зарегистрирован, но не активирован.');
        sleep(1);

        //good email
        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], $account_details[2]);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        sleep(1);

        $this->waitForTextPresent("На указанный вами email ". $account_details[2]);

        $this->open($this->getActivationKeyByEmail($account_details[2]));

        $this->waitForTextPresent("Рабочий кабинет");

        // проверить кол-во симуляций сразу после регистрации
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='0');

        //проверить данные в профиле
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['username'])==$account_details[0]);
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['profile']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate_profile']['name']);
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['corporate_profile']['name'])==$account_details[0]);
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['corporate_profile']['lastname'])==$account_details[1]);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate_profile']['email'])==$account_details[2]);


    }
}