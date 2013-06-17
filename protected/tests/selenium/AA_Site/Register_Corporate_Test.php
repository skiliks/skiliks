<?php
//

class Register_Corporate_Test extends SeleniumTestHelper
{

    public function test_Register_Corporate()
    {

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('http://test.skiliks.com/ru');

        //это линг регистрации в центре на главной
        $this->optimal_click("link=Получить бесплатный доступ");
        //$this->assertLocation('http://test.skiliks.com/registration');
        $this->waitForVisible("//div[@class='testtime']");
        $this->assertText("//div[@class='testtime']", '15 Минут');


        $new_email = "gty1991+";
        $new_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $new_email .= "@tanyaa.com";

        $this->type('id=YumProfile_email',$new_email);
        $this->type('id=YumUser_password','123123');
        $this->type('id=YumUser_password_again','123123');
        $this->optimal_click('id=YumUser_agree_with_terms');
        $this->clickAndWait("name=yt0");

        $this->assertTrue($this->isTextPresent('Активация'));
        sleep(3);
        echo TestUserHelper::getActivationUrl($new_email);
        $this->open(TestUserHelper::getActivationUrl($new_email));

        //sleep(30);

        $this->assertTrue($this->isTextPresent('можете'));
        $this->optimal_click("xpath=//*[@id='registration_check']");
        $this->optimal_click("xpath=//*[@id='registration_switch']");

        $this->waitForVisible("xpath=(//*[contains(text(),'Зарегистрируйтесь,')])");
        $this->type('id=YumProfile_firstname','test-name');
        $this->type('id=YumProfile_lastname','test-surname');
        $this->type('id=YumProfile_lastname','test-surname');

        $korp_email = "gty1991+";
        $korp_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $korp_email .= "@skiliks.com";

        $this->type('id=UserAccountCorporate_corporate_email', $korp_email);

        $this->optimal_click("link=Автомобильный");
        sleep(2);
        $this->optimal_click("link=Агропромышленный");

        $this->optimal_click("xpath=(//*[contains(text(),'Войти')])");
+
        $this->close();
    }
}