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
     * test_RegisterPersonal_SK4705() тестирует задачу SKILIKS-4705.
     */
    public function test_RegisterPersonal_SK4705()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->assertTextPresent('Запомнить меня');
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_popup']);
        $this->waitForVisible("css=.choose-account-button-span");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logo_img']);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['free_access2']);
        $this->waitForVisible("css=.choose-account-button-span");

        //check needed information for this page
        $this->assertTextPresent('Возможность получать приглашения от работодателя');
        $this->assertTextPresent('3 симуляции бесплатно (Полная версия)');
        $this->assertTextPresent('Зарегистрируйтесь, выбрав подходящий профиль');

        // register personal account
        $this->optimal_click("xpath=(//*[contains(text(),'Выбрать')])");

        //0 - personal
        $account_details = $this->setUserDetails(0);

        // all is empty
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Выберите профессиональный статус');
        $this->assertTextPresent('Введите имя');
        $this->assertTextPresent('Введите фамилию');
        $this->assertTextPresent('Введите email');
        $this->assertTextPresent('Введите пароль');
        $this->assertTextPresent('Подтвердите пароль');
        $this->assertTextPresent('Вы должны согласиться с условиями');
        sleep(1);
        $this->optimal_click("xpath=//a[contains(text(),'Выберите статус')]");
        $this->optimal_click("xpath=//a[contains(text(),'Функциональный менеджер')]");
        $this->type(Yii::app()->params['test_mappings']['site_register']['userName'],$account_details[0]);
        $this->type(Yii::app()->params['test_mappings']['site_register']['userSurname'],$account_details[1]);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);
        sleep(1);
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
        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], "emailForBan1@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Ваш аккаунт заблокирован');
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
        $this->waitForTextPresent("На указанный вами email ". $account_details[2]. " отправлено письмо");
        $this->open($this->getActivationKeyByEmail($account_details[2]));
        $this->waitForTextPresent("Личный кабинет");

        $this->assertTrue($this->getText("css=.top-profile.top-profile-persn")==$account_details[0]);
        $this->optimal_click(Yii::app()->params['test_mappings']['personal']['my_profile']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['personal']['prof_name']);
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['personal']['prof_name'])==$account_details[0]);
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['personal']['prof_surname'])==$account_details[1]);
    }

    /**
     * test_RegisterPersonal_SK4706() тестирует задачу SKILIKS-4706.
     */
    public function test_RegisterCorporate_SK4706()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['free_access1']);
        $this->waitForVisible("css=.choose-account-button-span");

        // register corporate account
        $account_details = $this->setUserDetails(1);
        $this->optimal_click("xpath=//a[contains(text(),'Выберите отрасль')]");
        $this->optimal_click("xpath=//a[contains(text(),'Лесная и деревообрабатывающая промышленность')]");
        $this->type(Yii::app()->params['test_mappings']['site_register']['userName'],$account_details[0]);
        $this->type(Yii::app()->params['test_mappings']['site_register']['userSurname'],$account_details[1]);
        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], "gty1991@gmail.com");
        $this->type(Yii::app()->params['test_mappings']['site_register']['password1'],$account_details[3]);
        $this->type(Yii::app()->params['test_mappings']['site_register']['password2'],$account_details[3]);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
        $this->waitForTextPresent('Введите корпоративный email');

        $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], $account_details[2]);
        $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);

        $this->waitForTextPresent("На указанный вами email ". $account_details[2]. " отправлено письмо");
        $this->open($this->getActivationKeyByEmail($account_details[2]));
        $this->waitForTextPresent("Рабочий кабинет");

    }
}