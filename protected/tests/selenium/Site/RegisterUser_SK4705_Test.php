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

        $this->clear_blocked_auth_users();

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

        $this->assertTextPresent('Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию');

        //0 - personal
        $account_details = $this->setUserDetails(1);

        $this->userRegisterInformation("", "", "", "", "", "", array("Введите имя", "Введите фамилию", "Введите email", "Введите пароль", "Подтвердите пароль", "Вы должны согласиться с условиями"));

        $this->userRegisterInformation("", $account_details[0], $account_details[1], " ", " ", 1, array("Введите email", "Введите пароль", "Подтвердите пароль"));

        $this->userRegisterInformation("", $account_details[0], $account_details[1], "123", "123", 0, array("Введите email", "Не менее 6 символов"));

        $this->userRegisterInformation("", $account_details[0], $account_details[1], "123123", "123123123", 0, array("Введите email", "Пароли не совпадают"));

        $this->userRegisterInformation("", $account_details[0], $account_details[1], "123123", "123123", 0, array("Введите email", "Пароль слишком простой"));

        $this->userRegisterInformation("", $account_details[0], $account_details[1], $account_details[3], $account_details[3], 0, array("Введите email"));

        $this->userRegisterInformation("wrongEmail", $account_details[0], $account_details[1], $account_details[3], $account_details[3], 0, array("Email введён неверно"));

        $this->userRegisterInformation("emailForBaned@skiliks.com", $account_details[0], $account_details[1], $account_details[3], $account_details[3], 0, array("заблокирован", "Данный email занят"));

        $this->userRegisterInformation("tetyana.grybok@skiliks.com", $account_details[0], $account_details[1], $account_details[3], $account_details[3], 0, array("Данный email занят"));

        $this->userRegisterInformation("emailNotActivated@skiliks.com", $account_details[0], $account_details[1], $account_details[3], $account_details[3], 0, array("не активирован"));

        $this->userRegisterInformation($account_details[2], $account_details[0], $account_details[1], $account_details[3], $account_details[3], 0, array("На указанный вами email"));

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

    public function userRegisterInformation($email, $name, $surname, $password1, $password2, $terms, $errors)
    {
        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], $email);
        $this->type(Yii::app()->params['test_mappings']['site_register']['userName'], $name);
        $this->type(Yii::app()->params['test_mappings']['site_register']['userSurname'], $surname);
        $this->type(Yii::app()->params['test_mappings']['site_register']['password1'], $password1);
        $this->type(Yii::app()->params['test_mappings']['site_register']['password2'], $password2);
        if ($terms==1)
        {
            $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);
        }
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        for ($i=0; $i<count($errors); $i++ )
        {
            sleep(1);
            $this->waitForVisible("xpath=(//*[contains(text(),'".$errors[$i]."')])");
        }
    }
}