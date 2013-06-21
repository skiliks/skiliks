<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для проверки регистрации персонального профиля без прохождения симуляции, проверкой ошибок ввода на формах,
 * (для SK3056)
 */
class Register_Personal_Test extends SeleniumTestHelper
{
    public function test_Personal_Corporate_SK3056()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('http://test.skiliks.com/ru');

        //это линк регистрации в центре на главной
        $this->optimal_click("link=Получить бесплатный доступ");

        $this->waitForVisible("//div[@class='testtime']");
        $this->assertText("//div[@class='testtime']", '15 Минут');

        //генерируем каждый раз новый персональный e-mail для пользователя
        $new_email = "gty1991_1+";
        $new_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $new_email .= "@mail.ru";

        $this->type('id=YumProfile_email',$new_email);
        $this->type('id=YumUser_password','123123');
        $this->type('id=YumUser_password_again','123123');
        $this->optimal_click('id=YumUser_agree_with_terms');
        $this->clickAndWait("name=yt0");

        $this->assertTrue($this->isTextPresent('Активация'));
        sleep(3);
        $this->open(TestUserHelper::getActivationUrl($new_email));

        sleep(5);
        $this->assertTrue($this->isTextPresent('можете'));
        $this->optimal_click("xpath=//*[@id='registration_check']");
        $this->optimal_click("xpath=//*[@id='registration_switch']");

        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Зарегистрируйтесь,')])");
        $this->type("css=#user-account-personal-form > div.row > div.field > #YumProfile_firstname",'test-name');
        $this->type("css=#user-account-personal-form > div.row > div.field > #YumProfile_lastname",'test-surname');

        $this->optimal_click("xpath=//div/section/div[1]/form/div[5]/div/input");
        sleep(5);
//
        $this->assertTrue($this->isVisible("css=#user-account-personal-form > div:nth-child(5) > div:nth-child(1) > input:nth-child(1)"));
        $this->close();
    }
}