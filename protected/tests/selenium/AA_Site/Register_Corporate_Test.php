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
        //это кнопка "Начать"
        $this->clickAndWait("name=yt0");
        $this->assertTrue($this->isTextPresent('Введите email','Введите пароль','Подтвердите пароль'));

        $this->type('id=YumProfile_email','empty');
        $this->type('id=YumUser_password','123123');
        $this->type('id=YumUser_password_again','1231231');
        $this->clickAndWait("name=yt0");
        $this->assertTrue($this->isTextPresent('Email введён неверно','Пароли не совпадают'));

        //это лого в футере
        $this->clickAndWait("link=Skiliks");
        $this->clickAndWait("css=footer > a.bigbtnsubmt.freeacess > cufon.cufon.cufon-canvas > canvas");
        //$this->assertLocation('http://test.skiliks.com/registration');
        $this->type('id=YumProfile_email','empty');
        $this->type('id=YumUser_password','1');
        $this->type('id=YumUser_password_again','1');
        $this->clickAndWait("name=yt0");
        $this->assertTrue($this->isTextPresent('Email введён неверно','Введите не менее 6 символов'));

        //это лого в хедере
        $this->clickAndWait("css=img[alt='Skiliks']");
        $this->clickAndWait("//ul[@id='yw0']/li[4]/a/cufon/canvas");
        $this->clickAndWait("//div[@id='top']/div[2]/div/div/div/div[2]/div[4]/a/cufon[2]/canvas");
        //$this->assertLocation('http://test.skiliks.com/registration');
        $this->type('id=YumProfile_email','asd@skilikscom');
        $this->type('id=YumUser_password_again','123123');
        $this->clickAndWait("name=yt0");
        $this->assertTrue($this->isTextPresent('Email введён неверно', 'Введите пароль'));

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
        $this->type("css=#user-account-corporate-form > div.row > div.field > #YumProfile_firstname",'test-name');
        $this->type("css=#user-account-corporate-form > div.row > div.field > #YumProfile_lastname",'test-surname');

        $korp_email = "gty1991+";
        $korp_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $korp_email .= "@skiliks.com";

        $this->type('css=#user-account-corporate-form > div.row > div.field > #UserAccountCorporate_corporate_email', $korp_email);

        $this->optimal_click("xpath=//div/section/div[2]/form/div[5]/div/input");

        $this->open(TestUserHelper::getCorporateActivationUrl($korp_email));
        sleep(5);

        $this->assertTrue($this->isVisible("xpath=//body/div[1]/div[1]/section/aside/div[1]/div[1]/form/div[4]/input"));
        $this->close();
    }
}