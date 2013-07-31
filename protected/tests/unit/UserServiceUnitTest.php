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

        $transaction = Yii::app()->db->beginTransaction();

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

        $transaction->rollback();

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
            $url = TestUserHelper::getCorporateActivationUrl("asd@skiliks.com");
            $this->assertNotNull($url);
        } else {
            $this->markTestSkipped();
        }
    }
}