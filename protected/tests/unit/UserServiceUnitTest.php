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
        $exist_profile_corporate = YumProfile::model()->findByAttributes(['email'=>'test-corporate-phpunit-account@skiliks.com']);
        if(null !== $exist_profile_corporate){
            YumUser::model()->deleteAllByAttributes(['id'=>$exist_profile_corporate->user_id]);
            YumProfile::model()->deleteAllByAttributes(['user_id'=>$exist_profile_corporate->user_id]);
            UserAccountCorporate::model()->deleteAllByAttributes(['user_id'=>$exist_profile_corporate->user_id]);
        }
        $exist_profile_personal = YumProfile::model()->findByAttributes(['email'=>'test-private-phpunit-account@skiliks.com']);
        if(null !== $exist_profile_personal){
            YumUser::model()->deleteAllByAttributes(['id'=>$exist_profile_personal->user_id]);
            YumProfile::model()->deleteAllByAttributes(['user_id'=>$exist_profile_personal->user_id]);
            UserAccountCorporate::model()->deleteAllByAttributes(['user_id'=>$exist_profile_personal->user_id]);
        }
        $user_corporate  = new YumUser('registration');
        $user_corporate->setAttributes(['password'=>'123123', 'password_again'=>'123123', 'agree_with_terms'=>'yes']);
        $profile_corporate  = new YumProfile('registration_corporate');
        $profile_corporate->setAttributes(['firstname'=>'Алексей', 'lastname'=>'Сафронов', 'email'=>'test-corporate-phpunit-account@skiliks.com']);
        $account_corporate = new UserAccountCorporate('corporate');
        $account_corporate->setAttributes(['industry_id'=>Industry::model()->findByAttributes(['label'=>'Другая'])->id]);
        $assert_result_corporate = UserService::createCorporateAccount($user_corporate, $profile_corporate, $account_corporate);
        $this->assertTrue($assert_result_corporate);
        $status_activation = YumUser::activate($profile_corporate->email, $user_corporate->activationKey);
        $this->assertInstanceOf('YumUser', $status_activation);
        $assert_profile_corporate = YumProfile::model()->findByAttributes(['email'=>'test-corporate-phpunit-account@skiliks.com']);
        $this->assertNotNull($assert_profile_corporate);
        $this->assertNotNull($assert_profile_corporate->user);
        $assert_account_corporate = UserAccountCorporate::model()->findByAttributes(['user_id'=>$assert_profile_corporate->user_id]);
        $this->assertNotNull($assert_account_corporate);
        /* @var $assert_account_corporate UserAccountCorporate */
        $this->assertEquals($assert_account_corporate->invites_limit, 3);

        $user_personal  = new YumUser('registration');
        $user_personal->setAttributes(['password'=>'123123', 'password_again'=>'123123', 'agree_with_terms'=>'yes']);
        $profile_personal  = new YumProfile('registration');
        $profile_personal->setAttributes(['firstname'=>'Альфред', 'lastname'=>'Хичкок', 'email'=>'test-private-phpunit-account@skiliks.com']);
        $account_personal = new UserAccountPersonal('personal');
        $account_personal->setAttributes(['professional_status_id'=>ProfessionalStatus::model()->findByAttributes(['label'=>'Студент'])->id]);
        $assert_result_personal = UserService::createPersonalAccount($user_personal, $profile_personal, $account_personal);
        $this->assertTrue($assert_result_personal);
        $status_activation = YumUser::activate($profile_personal->email, $user_personal->activationKey);
        $this->assertInstanceOf('YumUser', $status_activation);
        $assert_profile_personal = YumProfile::model()->findByAttributes(['email'=>'test-private-phpunit-account@skiliks.com']);
        $this->assertNotNull($assert_profile_personal);
        $this->assertNotNull($assert_profile_personal->user);
        $assert_account_personal = UserAccountPersonal::model()->findByAttributes(['user_id'=>$assert_profile_personal->user_id]);
        $this->assertNotNull($assert_account_personal);

        $vacancy = new Vacancy();
        $vacancy->professional_occupation_id = ProfessionalOccupation::model()->findByAttributes(['label'=>'Другая'])->id;
        $vacancy->professional_specialization_id = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Прочее'])->id;
        $vacancy->label = 'Круатор';
        $vacancy->position_level_slug = 'manager';
        $vacancy->user_id = $user_corporate->id;
        $vacancy->save(false);
        $invite = new Invite();
        $invite->setAttributes([
            'firstname'=>'Альфред',
            'lastname'=>'Хичкок',
            'email'=>'test-private-phpunit-account@skiliks.com',
            'vacancy_id'=>$vacancy->id,
            'fullname' => 'Альфред Хичкок',
            'message' => '',
            'is_display_simulation_results'=>'0'
        ]);
        $is_send = UserService::sendInvite($user_corporate, null, $invite, '0');
        $this->assertTrue($is_send);
        $assert_account_corporate->refresh();
        $invite->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 2);
        $this->assertEquals($invite->status, Invite::STATUS_PENDING);

        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $notUsedFullInvites = UserService::getInviteHimSelf($user_corporate, $fullScenario);

        $check = UserService::getSimulationContentsAndConfigs($user_corporate, '', 'promo', 'full', $notUsedFullInvites[0]->id);
        $this->assertTrue($check->return);
        $simulation = SimulationService::simulationStart($notUsedFullInvites[0], 'promo', 'tutorial');
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 1);

        $assert_account_corporate->addSimulations(5);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 6);

        UserService::inviteExpired();
        $invite->refresh();
        $this->assertEquals($invite->status, Invite::STATUS_PENDING);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 6);

        $invite->expired_at = date("Y-m-d H:i:s");
        $invite->save(false);

        UserService::inviteExpired();
        $invite->refresh();
        $this->assertEquals($invite->status, Invite::STATUS_EXPIRED);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 7);
        $invite2 = new Invite();
        $invite2->setAttributes([
            'firstname'=>'Альфред',
            'lastname'=>'Хичкок',
            'email'=>'test-private-phpunit-account@skiliks.com',
            'vacancy_id'=>$vacancy->id,
            'fullname' => 'Альфред Хичкок',
            'message' => '',
            'is_display_simulation_results'=>'0'
        ]);

        $is_send = UserService::sendInvite($user_corporate, null, $invite2, '0');
        $this->assertTrue($is_send);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 6);
        $declineExplanation = new DeclineExplanation();
        $declineExplanation->attributes = ['invite_id'=>$invite->id, 'reason_id'=>2, 'description'=>''];
        $decline_status = InviteService::declineInvite($user_personal, $declineExplanation);
        $this->assertNotNull($decline_status);
        $invite->refresh();
        $this->assertEquals($invite->status, Invite::STATUS_DECLINED);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 7);
    }

}