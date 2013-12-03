<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы регистрации персонального аккаунта по ссылке от корпоративного (для SK3373 и SK3364)
 */
class Registration_by_link_SK3373_Test extends SeleniumTestHelper
{
    /**
     * test_Registration_by_link_SK3373() тестирует задачу SKILIKS-3373
     */
    public function test_Registration_by_link_Accept_SK3373()
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
        sleep(3);

        $this->open('/dashboard');

        //проверить, что 0 симуляций на счету
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='0');

        //добавить нужное кол-во симуляций себе в аккаунт
        $this->open('/invite/add-10');
        sleep(3);
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
        sleep(3);

        //выслать персональному, которому было отправлено приглашение
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteName'],"InviteName");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteSurname'],"InviteSurname");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],$emailForNewInvite);
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->assertTrue($this->getText("css=.errorMessage.Invite_email")=="Приглашение уже отправлено");

        //переход по ссылке от работодателя
        $this->open($this->getInviteLink($emailForNewInvite));
        $this->waitForTextPresent("Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию");

        //проверка на валидацию ошибок при регистрации по ссылке
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['register_by_link']['error_status'])=="Выберите профессиональный статус");
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['register_by_link']['error_password'])=="Введите пароль");
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['register_by_link']['error_password_again'])=="Подтвердите пароль");
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['register_by_link']['error_terms'])=="Вы должны согласиться с условиями");
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['register_by_link']['invite_name'])=="InviteName");
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['register_by_link']['invite_surname'])=="InviteSurname");

        //очистить поля имя, фамилия
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['invite_name'],"");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['invite_surname'],"");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['register_by_link']['error_name'])=="Введите имя");
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['register_by_link']['error_surname'])=="Введите фамилию");

        //новое имя и фамилия, выбрать статус, ввести короткие пароли, открыть лиц. соглашение, закрыть, нажать на регистрацию
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['invite_name'],"NewInviteName");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['invite_surname'],"NewInviteSurname");
        $this->optimal_click("link=Выберите статус");
        $this->optimal_click("link=Функциональный менеджер");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password'],"123");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password_again'],"123");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['link_terms']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Тестирование навыков менеджера')])");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['close_terms_popup']);
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['checkbox_terms']);
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['register_by_link']['error_password'])=="Не менее 6 символов");

        //пароли, которые не совпадают
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password'],"123123");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password_again'],"1231234");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['register_by_link']['error_password'])=="Пароли не совпадают");

        //правильные пароли
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password'],"123123");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password_again'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        $this->waitForTextPresent("Личный кабинет");

        //проверка, что у персонального пользователя имя и фамилия те, что были введены приглашенным, а не пригласившим
        $this->waitForVisible(Yii::app()->params['test_mappings']['personal']['username']);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['personal']['username'])=="NewInviteName");

        //проверка, что в таблице есть приглашение
        $this->waitForVisible(Yii::app()->params['test_mappings']['personal']['accept_invite']);
    }

    public function test_Registration_by_link_Decline_SK3373()
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
        sleep(3);

        $this->open('/dashboard');

        //проверить, что 0 симуляций на счету
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='0');

        //добавить нужное кол-во симуляций себе в аккаунт
        $this->open('/invite/add-10');
        sleep(3);
        $this->open('/dashboard');
        $this->waitForTextPresent("Рабочий кабинет");
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='10');

        //негативные кейсы на валидацию ошибок имени, фамилии, почты, позиции

        //имя и фамилия введены, почта неправильная
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteName'],"InviteName");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteSurname'],"InviteSurname");
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
        sleep(3);

        //переход по ссылке от работодателя
        $this->open($this->getInviteLink($emailForNewInvite));
        $this->waitForTextPresent("Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию");

        //проверка возможности отклонить приглашение
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['decline_register']);
        $this->waitForTextPresent("Пожалуйста, укажите причину отказа");

        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['back_to_registration']);
        $this->waitForTextPresent("Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию");

        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['decline_register']);
        $this->waitForTextPresent("Пожалуйста, укажите причину отказа");

        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['confirm_decline_invite']);
        sleep(3);
        $this->waitForTextPresent("Необходимо заполнить поле Причина отказа");

        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['decline_reason_0']);
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['confirm_decline_invite']);

        $this->waitForTextPresent("Спасибо за Ваш ответ!");
    }
}