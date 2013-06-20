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
        $this->waitForVisible("//div[@class='testtime']");
        $this->assertText("//div[@class='testtime']", '15 Минут');


        $new_email = "gty1991+";
        $new_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $new_email .= "@gmail.com";

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
        $this->type("css=#user-account-corporate-form > div.row > div.field > #YumProfile_firstname",'test-name');
        $this->type("css=#user-account-corporate-form > div.row > div.field > #YumProfile_lastname",'test-surname');

        $korp_email = "gty1991+";
        $korp_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $korp_email .= "@skiliks.com";

        $this->type('css=#user-account-corporate-form > div.row > div.field > #UserAccountCorporate_corporate_email', $korp_email);

        $this->optimal_click("xpath=//div/section/div[2]/form/div[5]/div/input");

        $this->open(TestUserHelper::getCorporateActivationUrl($korp_email));
        sleep(5);

        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Рабочий')])"));
        $this->close();
    }
}