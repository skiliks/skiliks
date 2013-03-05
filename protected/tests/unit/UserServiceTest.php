<?php

/**
 *
 */
class UserServiceTest extends CDbTestCase
{
    public function testAddNotifyMeEmail()
    {
        $goodEmail = 'test@test.com';
        $badEmail = 'blablabla-test';

        $transaction = Yii::app()->db->beginTransaction();

        UserService::addUserSubscription($goodEmail);
        UserService::addUserSubscription($badEmail);
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
}