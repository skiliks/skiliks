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

        // Очиста данных
        // 2 раза один и тот-же емейл добавить нельзя
        EmailsSub::model()->deleteAllByAttributes(['email' => $goodEmail]);

        $result = UserService::addUserSubscription($goodEmail);
        $this->assertEquals($result['result'],1);

        $result = UserService::addUserSubscription($badEmail);
        $this->assertEquals($result['result'],0);

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

    /**
     * Тест проверяет регистрацию корпоративного и персонального аккаунта,
     * а также рефералов, движение инвайтов, списание и зачисление их через
     * админку и устаревание и отклонение инвайтов.
     */
    public function testInvitesMovementAndUserRegistration() {

        //Удаляем тестовых юзеров если такие есть чтоб создать потом заново
        $exist_ref = UserReferral::model()->findByAttributes(['referral_email'=>'referall-unit-text@kiliks.com']);
        if( null !== $exist_ref ) {
            $exist_ref->delete();
        }
        $exist_ref2 = UserReferral::model()->findByAttributes(['referral_email'=>'referall-unit-text2@kiliks.com']);
        if( null !== $exist_ref2 ) {
            $exist_ref2->delete();
        }
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
        $exist_profile_referral = YumProfile::model()->findByAttributes(['email'=>'referall-unit-text@kiliks.com']);

        if(null !== $exist_profile_referral){
            YumUser::model()->deleteAllByAttributes(['id'=>$exist_profile_referral->user_id]);
            YumProfile::model()->deleteAllByAttributes(['user_id'=>$exist_profile_referral->user_id]);
            UserAccountCorporate::model()->deleteAllByAttributes(['user_id'=>$exist_profile_referral->user_id]);
        }
        $exist_profile_referral2 = YumProfile::model()->findByAttributes(['email'=>'referall-unit-text2@kiliks.com']);

        if(null !== $exist_profile_referral2){
            YumUser::model()->deleteAllByAttributes(['id'=>$exist_profile_referral2->user_id]);
            YumProfile::model()->deleteAllByAttributes(['user_id'=>$exist_profile_referral2->user_id]);
            UserAccountCorporate::model()->deleteAllByAttributes(['user_id'=>$exist_profile_referral2->user_id]);
        }

        //Создаем корпоративного пользователя для тестов
        $user_corporate  = new YumUser('registration');
        $user_corporate->setAttributes(['password'=>'123123', 'password_again'=>'123123', 'agree_with_terms'=>'yes']);
        $profile_corporate  = new YumProfile('registration_corporate');
        $profile_corporate->setAttributes(['firstname'=>'Алексей', 'lastname'=>'Сафронов', 'email'=>'test-corporate-phpunit-account@skiliks.com']);
        $account_corporate = new UserAccountCorporate('corporate');
        $account_corporate->setAttributes(['industry_id'=>Industry::model()->findByAttributes(['label'=>'Другая'])->id]);
        $assert_result_corporate = UserService::createCorporateAccount($user_corporate, $profile_corporate, $account_corporate);
        $this->assertTrue($assert_result_corporate);

        //Активируем его
        $status_activation = YumUser::activate($profile_corporate->email, $user_corporate->activationKey);
        $this->assertInstanceOf('YumUser', $status_activation);
        $assert_profile_corporate = YumProfile::model()->findByAttributes(['email'=>'test-corporate-phpunit-account@skiliks.com']);

        //Проверяем что пользователь добавлен и у него корпоративный аккаунт
        $this->assertNotNull($assert_profile_corporate);
        $this->assertNotNull($assert_profile_corporate->user);
        $assert_account_corporate = UserAccountCorporate::model()->findByAttributes(['user_id'=>$assert_profile_corporate->user_id]);
        $this->assertNotNull($assert_account_corporate);

        //Проверяем что в аккаунт добавлено 3 симуляции
        /* @var $assert_account_corporate UserAccountCorporate */
        $this->assertEquals($assert_account_corporate->invites_limit, 3);

        //Создаем персонального пользователя
        $user_personal  = new YumUser('registration');
        $user_personal->setAttributes(['password'=>'123123', 'password_again'=>'123123', 'agree_with_terms'=>'yes']);
        $profile_personal  = new YumProfile('registration');
        $profile_personal->setAttributes(['firstname'=>'Альфред', 'lastname'=>'Хичкок', 'email'=>'test-private-phpunit-account@skiliks.com']);
        $account_personal = new UserAccountPersonal('personal');
        $account_personal->setAttributes(['professional_status_id'=>ProfessionalStatus::model()->findByAttributes(['label'=>'Студент'])->id]);
        $assert_result_personal = UserService::createPersonalAccount($user_personal, $profile_personal, $account_personal);
        $this->assertTrue($assert_result_personal);

        //Активируем его
        $status_activation = YumUser::activate($profile_personal->email, $user_personal->activationKey);
        $this->assertInstanceOf('YumUser', $status_activation);
        $assert_profile_personal = YumProfile::model()->findByAttributes(['email'=>'test-private-phpunit-account@skiliks.com']);

        //Проверяем что пользователь создан и у него персональный аккаунт
        $this->assertNotNull($assert_profile_personal);
        $this->assertNotNull($assert_profile_personal->user);
        $assert_account_personal = UserAccountPersonal::model()->findByAttributes(['user_id'=>$assert_profile_personal->user_id]);
        $this->assertNotNull($assert_account_personal);

        //Создаем тестовую вакансию и наполняем произвольными данными
        $vacancy = new Vacancy();
        $vacancy->professional_occupation_id = ProfessionalOccupation::model()->findByAttributes(['label'=>'Другая'])->id;
        $vacancy->professional_specialization_id = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Прочее'])->id;
        $vacancy->label = 'Круатор';
        $vacancy->position_level_slug = 'manager';
        $vacancy->user_id = $user_corporate->id;
        $vacancy->save(false);

        //Создаем и отправляем инвайт от корпоративного к персональному
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

        //Проверяем что у корпоративного пользователя снята одна симуляция и статус инвайта в ожидании
        $this->assertEquals($assert_account_corporate->invites_limit, 2);
        $this->assertEquals($invite->status, Invite::STATUS_PENDING);

        //Получаем обьект сценария Full
        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        //Создаем инвайт full симуляции сам себе для корпоративного пользователя
        $notUsedFullInvites = UserService::getSelfToSelfInvite($user_corporate, $fullScenario);

        //Получаем конфиги для старта симуляции
        $check = UserService::getSimulationContentsAndConfigs($user_corporate, '', 'promo', 'full', $notUsedFullInvites[0]->id);
        $this->assertTrue($check->return);

        //Запускаем симуляцию
        $simulation = SimulationService::simulationStart($notUsedFullInvites[0], 'promo', 'tutorial');
        $simulation->refresh();

        //Проверяем что симуляция в статусе начато и и снята одна симуляция с корпоративного аккаунта
        $this->assertEquals($simulation->status, Simulation::STATUS_IN_PROGRESS);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 1);

        //Запускаем симуляцию ещё раз по данному инвайту
        $simulation2 = SimulationService::simulationStart($notUsedFullInvites[0], 'promo', 'tutorial');
        $assert_account_corporate->refresh();

        //Проверяем что неснято лишней симуляции с аккаунта
        $this->assertEquals($assert_account_corporate->invites_limit, 1);

        $simulation->refresh();
        $simulation2->refresh();

        //Проверяем что у первой симуляции статус прервано а в второй начато
        $this->assertEquals($simulation->status, Simulation::STATUS_INTERRUPTED);
        $this->assertEquals($simulation2->status, Simulation::STATUS_IN_PROGRESS);

        //Добавляем в аккаунт 5 инвайтов
        $assert_account_corporate->addSimulations(5);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 6);

        //Делаем Expired инвайтов и проверяем что они не устарели
        InviteService::makeExpiredInvitesExpired();

        $invite->refresh();
        $this->assertEquals($invite->status, Invite::STATUS_PENDING);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 6);

        //Проставляем одному инвайту Expired время
        $invite->expired_at = date("Y-m-d H:i:s");
        $invite->save(false);

        //Делаем Expired инвайтов и проверяем что он устарел и вернулся в аккаунт
        InviteService::makeExpiredInvitesExpired();
        $invite->refresh();
        $this->assertEquals($invite->status, Invite::STATUS_EXPIRED);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 7);

        //Создаем ещё одно приглашение персональному аккаунту
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

        //Отклоняем этот инвайт и проверяем его статус и что вернулась симуляция
        $declineExplanation = new DeclineExplanation();
        $declineExplanation->attributes = ['invite_id'=>$invite->id, 'reason_id'=>2, 'description'=>''];
        $decline_status = InviteService::declineInvite($user_personal, $declineExplanation);
        $this->assertNotNull($decline_status);
        $invite->refresh();
        $this->assertEquals($invite->status, Invite::STATUS_DECLINED);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 7);

        //Тест метода списания и начисления
        $assert_account_corporate->changeInviteLimits(5);

        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 12);

        //Снимаем 4 инвайта с обычного счета и проверяем что все врено
        $assert_account_corporate->changeInviteLimits(-4);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 8);

        //Задаем 4 инвайта за рефералов и снимаем 10, проверяем что осталось только 2 за рефералов
        $assert_account_corporate->referrals_invite_limit = 4;
        $assert_account_corporate->save(false);
        $assert_account_corporate->changeInviteLimits(-10);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 0);
        $this->assertEquals($assert_account_corporate->referrals_invite_limit, 2);

        //Списываем 4 и проверяем что у нас 0 на обоих счетах
        $assert_account_corporate->changeInviteLimits(-4);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 0);
        $this->assertEquals($assert_account_corporate->referrals_invite_limit, 0);


        //Создаем приглашение для реферала и регистрируем его
        $referral = new UserReferral();
        $referral->referral_email = strtolower('referall-unit-text@kiliks.com');
        $result = UserService::addReferralUser($user_corporate, $referral);
        $this->assertTrue($result);

        $user_referral  = new YumUser('registration');
        $user_referral->setAttributes(['password'=>'123123', 'password_again'=>'123123', 'agree_with_terms'=>'yes']);
        $profile_referral  = new YumProfile('registration_corporate');
        $profile_referral->setAttributes(['firstname'=>'Августин', 'lastname'=>'Пупанов']);
        $account_referral = new UserAccountCorporate('corporate');
        $account_referral->setAttributes(['industry_id'=>Industry::model()->findByAttributes(['label'=>'Другая'])->id]);


        //Yii::app()->user->data()->logout();
        $result = UserService::createReferral($user_referral, $profile_referral, $account_referral, $referral);
        $this->assertTrue($result);

        $assert_account_corporate->refresh();

        //Пооверяем что корпоративному добавился инвайт за реферала
        $this->assertEquals($assert_account_corporate->referrals_invite_limit, 1);

        //Сздаем второго реферала с таким же доменом
        $referral2 = new UserReferral();
        $referral2->referral_email = strtolower('referall-unit-text2@kiliks.com');
        $result = UserService::addReferralUser($user_corporate, $referral2);
        $this->assertTrue($result);

        $user_referral2  = new YumUser('registration');
        $user_referral2->setAttributes(['password'=>'123123', 'password_again'=>'123123', 'agree_with_terms'=>'yes']);
        $profile_referral2  = new YumProfile('registration_corporate');
        $profile_referral2->setAttributes(['firstname'=>'Августин', 'lastname'=>'Пупанов']);
        $account_referral2 = new UserAccountCorporate('corporate');
        $account_referral2->setAttributes(['industry_id'=>Industry::model()->findByAttributes(['label'=>'Другая'])->id]);


        //Yii::app()->user->data()->logout();
        $result = UserService::createReferral($user_referral2, $profile_referral2, $account_referral2, $referral2);
        $this->assertTrue($result);

        $assert_account_corporate->refresh();

        //Проверяем что у нас не добавилось лишних преглашений
        $this->assertEquals($assert_account_corporate->referrals_invite_limit, 1);

        //Добавляем на основной счет инвайты и проверяем что все правильно
        $assert_account_corporate->changeInviteLimits(5);

        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 5);

    }

    /*public function testDebug(){

        SimulationService::CalculateTheEstimate(565, 'tetyana.grybok@skiliks.com');

    }*/

}