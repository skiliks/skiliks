<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для создания пользователей для тестов по регистрации, авторизации и прочему - забаненный и не авторизованный (для SK5854)
 */
class Add_User_SK5854_Test extends SeleniumTestHelper
{
    /**
     * test_Add_Banned_User_SK5854() регистрирует пользователя для тестов (Забаненый пользователь)
     */
    public function test_Add_Banned_User_SK5854()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->open('/ru');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'tetyana.grybok@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'skiliks123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);

        $this->open('/admin_area/dashboard');

        $email = "emailForBaned@skiliks.com";

        $this->type(Yii::app()->params['test_admin_mappings']['home_page']['quick_view_email'], $email);
        $this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['quick_view_find']);
        sleep(2);

        if ($this->isElementPresent("css=.alert.alert-error"))
        {
            //регистрация пользователя и его активация и бан
            $this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['logout']);

            $this->open('/registration');
            $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], $email);
            $this->type(Yii::app()->params['test_mappings']['site_register']['userName'], "Baned");
            $this->type(Yii::app()->params['test_mappings']['site_register']['userSurname'], "Email");
            $this->optimal_click("xpath=//div/div[2]/div/form/div[2]/div/a[2]");
            $this->mouseOver("xpath=//*[@id='registration-form']/div[2]/div/ul/li[2]/a");
            $this->optimal_click("xpath=//*[@id='registration-form']/div[2]/div/ul/li[2]/a");
            $this->type(Yii::app()->params['test_mappings']['site_register']['password1'], "skiliks123123");
            $this->type(Yii::app()->params['test_mappings']['site_register']['password2'], "skiliks123123");
            $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);
            $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
            $this->waitForVisible("xpath=(//*[contains(text(),'На указанный вами email')])");

            $this->open($this->getActivationKeyByEmail($email));
            $this->waitForTextPresent("Рабочий кабинет");
            $this->optimal_click(Yii::app()->params['test_mappings']['site']['logOut']);

            $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
            $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
            $this->type(Yii::app()->params['test_mappings']['site']['username'],'tetyana.grybok@skiliks.com');
            $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'skiliks123123');
            $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
            $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);

            $this->open('/admin_area/dashboard');

            $this->type(Yii::app()->params['test_admin_mappings']['home_page']['quick_view_email'],$email);
            $this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['quick_view_find']);

            $this->optimal_click(Yii::app()->params['test_admin_mappings']['corporate_info']['account_ban']);
            $this->waitForVisible("css=.alert.alert-success");

            /*$this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['logout']);

            // проверить авторизация работает или нет?
            $this->open('/ru');
            $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
            $this->type(Yii::app()->params['test_mappings']['site']['username'], $email);
            $this->type(Yii::app()->params['test_mappings']['site']['userpass'], "skiliks123123");
            $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
            $this->waitForVisible("xpath=(//*[contains(text(),'заблокирован')])");*/
        }
    }

    /**
     * test_Add_NotActivated_User_SK5854() регистрирует пользователя для тестов (Неактивированный пользователь)
     */
    public function test_Add_NotActivated_User_SK5854()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->open('/ru');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'tetyana.grybok@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'skiliks123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);

        $this->open('/admin_area/dashboard');

        $email = "emailNotActivated@skiliks.com";

        $this->type(Yii::app()->params['test_admin_mappings']['home_page']['quick_view_email'],$email);
        $this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['quick_view_find']);
        sleep(2);

        if ($this->isElementPresent("css=.alert.alert-error"))
        {
            $this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['logout']);

            $this->open('/registration');
            $this->type(Yii::app()->params['test_mappings']['site_register']['userEmail'], $email);
            $this->type(Yii::app()->params['test_mappings']['site_register']['userName'], "NotActivated");
            $this->type(Yii::app()->params['test_mappings']['site_register']['userSurname'], "Email");
            $this->optimal_click("xpath=//div/div[2]/div/form/div[2]/div/a[2]");
            $this->mouseOver("xpath=//*[@id='registration-form']/div[2]/div/ul/li[2]/a");
            $this->optimal_click("xpath=//*[@id='registration-form']/div[2]/div/ul/li[2]/a");
            $this->type(Yii::app()->params['test_mappings']['site_register']['password1'], "skiliks123123");
            $this->type(Yii::app()->params['test_mappings']['site_register']['password2'], "skiliks123123");
            $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['checkbox_terms']);
            $this->optimal_click(Yii::app()->params['test_mappings']['site_register']['register_button']);
            $this->waitForVisible("xpath=(//*[contains(text(),'На указанный вами email')])");

            /*$this->open('/ru');
            $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
            $this->type(Yii::app()->params['test_mappings']['site']['username'], $email);
            $this->type(Yii::app()->params['test_mappings']['site']['userpass'], "skiliks123123");
            $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);
            $this->waitForVisible("xpath=(//*[contains(text(),'не активирован')])");*/
        }
    }
}
