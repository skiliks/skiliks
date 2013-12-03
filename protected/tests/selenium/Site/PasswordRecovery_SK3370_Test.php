<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы попапа Вход (4й пункт тест-плана по сайту) (для SK3222)
 */
class PasswordRecovery_SK3370_Test extends SeleniumTestHelper
{
    /**
     * test_PasswordRecovery_SK3370() тестирует задачу SKILIKS-3370.
     */
    public function test_PasswordRecovery_SK3370()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');
        $email = "selenium.engine@skiliks.com";
        //кликаем на Вход
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->assertTextPresent('Запомнить меня');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['recovery']);
        $this->assertElementPresent(Yii::app()->params['test_mappings']['site']['recovery_email']);

        $this->type(Yii::app()->params['test_mappings']['site']['recovery_email'],"");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['recovery_button']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Введите email')])");

        $this->type(Yii::app()->params['test_mappings']['site']['recovery_email'],"wrongEmail");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['recovery_button']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Email введён неверно')])");

        $this->type(Yii::app()->params['test_mappings']['site']['recovery_email'],"notRegisteredEmail@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['recovery_button']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Отсутствующий email')])");

       /* $this->type(Yii::app()->params['test_mappings']['site']['recovery_email'],"emailForBan@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['recovery_button']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Ваш аккаунт заблокирован')])");*/

        $this->type(Yii::app()->params['test_mappings']['site']['recovery_email'],$email);
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['recovery_button']);
        $this->waitForVisible("xpath=(//*[contains(text(),'На ваш email выслана инструкция по смене пароля.')])");
        //проверить, что письмо есть в очереди событий
        sleep(10);
        if ($this->findRecoveryMailInQueue($email)==true)
        {
            //перереход по ссылке
            $this->open($this->createRecoveryURL($this->findUserIdByEmail($email)));
            //кейсы восстановления проверить
            $this->type(Yii::app()->params['test_mappings']['site']['change_pass'],"123123");
            $this->type(Yii::app()->params['test_mappings']['site']['verify_pass'],"123123");
            $this->optimal_click(Yii::app()->params['test_mappings']['site']['save_new_pass']);
            $this->waitForVisible("xpath=(//*[contains(text(),'Новый пароль успешно сохранен')])");
        }
        else
            $this->fail("There is no letter for recover password in database");
    }

    private function createRecoveryURL ($id)
    {
        /* @var YumUser $yumUser */
        $yumUser = YumUser::model()->findByPk($id);
        $key = $yumUser->activationKey;
        $email = strtolower($yumUser->profile->email);
        return "/recovery?key=". $key. "&email=". $email;
    }

    private function findUserIdByEmail ($emailU)
    {
        /* @var YumProfile $profile */
        $profile = YumProfile::model()->find('email=:email', array(':email'=>$emailU));
        return $profile->user_id;
    }

    private function findRecoveryMailInQueue ($email)
    {
        /* @var  EmailQueue $letter */
        $criteria=new CDbCriteria;
        $criteria->condition= 'recipients=:recipients';
        $criteria->order= 't.id DESC';
        $criteria->limit= 1;
        $criteria->params= array(':recipients'=>$email);
        try{
            $letter = EmailQueue::model()->find($criteria);
            if (($letter->recipients==$email)&&($letter->subject == "Восстановление пароля к skiliks.com"))
                return true;
            else
                return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}