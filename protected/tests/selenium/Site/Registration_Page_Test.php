<?php

//пока что быдловерсия теста, который ходит на страницу регистрации по разным маппингам
// и проверяет error msg на странице регистрации
class Registration_Page_Test extends SeleniumTestHelper
{

    public function test_SK2294()
    {

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        //это линг регистрации в центре на главной
        $this->optimal_click("link=Получить бесплатный доступ");
        //$this->assertLocation('http://test.skiliks.com/registration');
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
        $this->close();
    }
}