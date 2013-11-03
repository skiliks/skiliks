<?php

/**
 *
 */
class UserServiceUnitTest extends CDbTestCase
{
    public function testAddNotifyMeEmail()
    {
        $goodEmail = 'test@test.com';
        $badEmail = 'blablabla-test';

        $result = UserService::addUserSubscription($goodEmail);
        $this->assertEquals($result['result'],1);

        $result = UserService::addUserSubscription($badEmail);
        $this->assertEquals($result['result'],0);
        UserService::addUserSubscription($goodEmail);

        $forGood = EmailsSub::model()->findAllByAttributes([
            'email' => $goodEmail
        ]);

        $forBad = EmailsSub::model()->findAllByAttributes([
            'email' => $badEmail
        ]);
        $this->assertCount(1, $forGood);
        $this->assertCount(0, $forBad);
    }

    /**
     * test that TestUserHelper::getCorporateActivationUrl return string :)
     */
    public function testActivationKey() {
        /** @var YumProfile $user */
        $profile = YumProfile::model()->findByAttributes(['email' => 'asd@skiliks.com']);

        if ($profile->user->isCorporate()) {
            $url = TestUserHelper::getActivationUrl("asd@skiliks.com");
            $this->assertNotNull($url);
        } else {
            $this->markTestSkipped();
        }
    }

    public function testInvite() {
        /* @var $profile YumProfile */
        $profile = YumProfile::model()->findByAttributes(['email'=>'test-corporate-phpunit-account@skiliks.com']);
        if(null !== $profile){
            YumUser::model()->deleteAllByAttributes(['id'=>$profile->user_id]);
            YumProfile::model()->deleteAllByAttributes(['user_id'=>$profile->user_id]);
            UserAccountCorporate::model()->deleteAllByAttributes(['user_id'=>$profile->user_id]);
        }
        $user  = new YumUser('registration');
        $user->setAttributes(['password'=>'123123', 'password_again'=>'123123', 'agree_with_terms'=>'yes']);
        $profile  = new YumProfile('registration_corporate');
        $profile->setAttributes(['firstname'=>'Алексей', 'lastname'=>'Сафронов', 'email'=>'test-corporate-phpunit-account@skiliks.com']);
        $account_corporate = new UserAccountCorporate('corporate');
        $account_corporate->setAttributes(['industry_id'=>27]);
        UserService::createCorporateAccount($user, $profile, $account_corporate);
        $assert_profile = YumProfile::model()->findByAttributes(['email'=>'test-corporate-phpunit-account@skiliks.com']);
        $this->assertNotNull($assert_profile);
        $this->assertNotNull($assert_profile->user);
        $assert_account_corporate = YumProfile::model()->findByAttributes(['user_id'=>$assert_profile->user_id]);
        $this->assertNotNull($assert_account_corporate);
    }

}