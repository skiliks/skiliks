<?php
//

class Register_Corporate_Test extends SeleniumTestHelper
{

    public function test_Register_Corporate()
    {

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        //это линг регистрации в центре на главной
        $this->optimal_click("link=Получить бесплатный доступ");
        //$this->assertLocation('http://test.skiliks.com/registration');
        $this->waitForVisible("//div[@class='testtime']");
        $this->assertText("//div[@class='testtime']", '15 Минут');


        $new_email = "test-email+";
        $new_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $new_email .= "@skiliks.mail";

        $this->type('id=YumProfile_email',$new_email);
        $this->type('id=YumUser_password','123123');
        $this->type('id=YumUser_password_again','123123');
        $this->optimal_click('id=YumUser_agree_with_terms');
        $this->clickAndWait("name=yt0");

        $this->assertTrue($this->isTextPresent('Активация'));
        echo $new_email;
        sleep(10);

        $this->open(TestUserHelper::getActivationUrl($new_email));
        echo TestUserHelper::getActivationUrl($new_email);
        sleep(30);

        $this->close();
    }
}