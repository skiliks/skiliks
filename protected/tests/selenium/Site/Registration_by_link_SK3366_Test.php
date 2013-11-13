<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы регистрации персонального аккаунта по ссылке от корпоративного (для SK3366 и SK3364)
 */
class Registration_by_link_SK3366_Test extends SeleniumTestHelper
{
    /**
     * test_Registration_by_link_SK3366() тестирует задачу SKILIKS-3366
     */
    public function test_Registration_by_link_SK3366()
    {
        // Пока считаем, что позиция уже добавлена пользователем
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'selenium.engine@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);

        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['username'])=="seleniumEngine");

        $invites="-". $this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit']);

        $this->open('/admin_area/user/4/details');
        $this->waitForVisible("xpath=//div[2]/div/div[2]/table/tbody/tr[5]/td[4]/form/input[1]");
        $this->type("xpath=//div[2]/div/div[2]/table/tbody/tr[5]/td[4]/form/input[1]",$invites);
        $this->optimal_click("css=#add_invites_button");
        sleep(5);

        $this->open('/dashboard');

        //проверить, что 0 симуляций на счету
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='0');

        //добавить нужное кол-во симуляций себе в аккаунт
        $this->open('/invite/add-10');
        sleep(5);
        $this->open('/dashboard');
        $this->waitForTextPresent("Рабочий кабинет");
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='10');

        //негативные кейсы на валидацию ошибок имени, фамилии, почты, позиции

        //все пустые значения
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->assertTrue($this->getText("css=.errorMessage.Invite_firstname")=="Введите имя");
        $this->assertTrue($this->getText("css=.errorMessage.Invite_lastname")=="Введите фамилию");
        $this->assertTrue($this->getText("css=.errorMessage.Invite_email")=="Введите email");

        //имя и фамилия введены, почта неправильная
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteName'],"InviteName");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteSurname'],"InviteSurname");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],"email");
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->assertTrue($this->getText("css=.errorMessage.Invite_email")=="Email введён неверно");

        //почта своя
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],"selenium.engine@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->waitForVisible("xpath=(//*[contains(text(),'Данный пользователь с e-mail: selenium.engine@skiliks.com')])");
        $this->optimal_click("css=.popupclose");
        $this->assertTrue($this->getText("css=.errorMessage.Invite_email")=="Действие невозможно");

        //почта корпоративная
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],"selenium.assessment@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->waitForVisible("xpath=(//*[contains(text(),'Данный пользователь с e-mail: selenium.assessment@skiliks.com')])");
        $this->optimal_click("css=.popupclose");

        //выслать персональному, которому было отправлено приглашение
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],"gty1991@gmail.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->assertTrue($this->getText("css=.errorMessage.Invite_email")=="Приглашение уже отправлено");

        //выслать уникальному персональному
        $emailForNewInvite = "invite+";
        $emailForNewInvite .= (string)rand(1, 10000)+(string)rand(1,500)+(string)rand(1,200);
        $emailForNewInvite .= "@pers";
        $emailForNewInvite .= (string)rand(1, 500)+(string)rand(1,50)+(string)rand(1,10);
        $emailForNewInvite .= ".skil.com";
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],$emailForNewInvite);
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->waitForVisible(Yii::app()->params['test_mappings']['popup_send_invite']['fullName']);
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['popup_send_invite']['fullName'])=="InviteName InviteSurname");
        $this->optimal_click(Yii::app()->params['test_mappings']['popup_send_invite']['send']);
        sleep(5);


    }
}