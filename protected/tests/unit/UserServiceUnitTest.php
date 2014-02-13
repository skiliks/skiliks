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

    public function deleteUsersByEmail(array $emails) {

        foreach($emails as $email) {
            $user = YumProfile::model()->findByAttributes(['email'=>$email]);
            if(null !== $user) {
                Invoice::model()->deleteAllByAttributes(['user_id'=>$user->user_id]);
                UserReferral::model()->deleteAllByAttributes(['referrer_id'=>$user->user_id]);
                YumUser::model()->deleteAllByAttributes(['id'=>$user->user_id]);
                YumProfile::model()->deleteAllByAttributes(['user_id'=>$user->user_id]);
                UserAccountCorporate::model()->deleteAllByAttributes(['user_id'=>$user->user_id]);
                UserAccountPersonal::model()->deleteAllByAttributes(['user_id'=>$user->user_id]);
            }
        }

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
        $this->deleteUsersByEmail([
            'test-corporate-phpunit-account@skiliks.com',
            'test-private-phpunit-account@skiliks.com',
            'referall-unit-text@kiliks.com',
            'referall-unit-text2@kiliks.com']);
        //Создаем корпоративного пользователя для тестов
        $user_corporate  = new YumUser('registration');
        $user_corporate->setAttributes(['password'=>'Skiliks123123', 'password_again'=>'Skiliks123123', 'agree_with_terms'=>'yes']);
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

        /* @var UserAccountCorporate $assert_account_corporate */
        $assert_account_corporate = UserAccountCorporate::model()->findByAttributes(['user_id'=>$assert_profile_corporate->user_id]);
        $this->assertNotNull($assert_account_corporate);

        $this->assertEquals($assert_account_corporate->expire_invite_rule, UserAccountCorporate::EXPIRE_INVITE_RULE_BY_TARIFF);

        //Проверяем что в аккаунт добавлено 3 симуляции
        /* @var $assert_account_corporate UserAccountCorporate */
        $this->assertEquals($assert_account_corporate->invites_limit, 3);

        //Создаем персонального пользователя
        $user_personal  = new YumUser('registration');
        $user_personal->setAttributes(['password'=>'Skiliks123123', 'password_again'=>'Skiliks123123', 'agree_with_terms'=>'yes']);
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
        $user_referral->setAttributes(['password'=>'Skiliks123123', 'password_again'=>'Skiliks123123', 'agree_with_terms'=>'yes']);
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
        $user_referral2->setAttributes(['password'=>'Skiliks123123', 'password_again'=>'Skiliks123123', 'agree_with_terms'=>'yes']);
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

        /* @var $my_invite Invite */

        $invite2->refresh();
        $this->assertEquals($invite2->status, Invite::STATUS_PENDING);

        $invite2->expired_at = (new DateTime())->format("Y-m-d H:i:s");
        $invite2->save(false);

        /* @var $tariff Tariff */
        $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_LITE]);

        $assert_account_corporate->setTariff($tariff, true);

        InviteService::makeExpiredInvitesExpired();

        $invite2->refresh();
        $this->assertEquals($invite2->status, Invite::STATUS_EXPIRED);

        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, $tariff->simulations_amount + 1 );//Возвращена симуляция 1 за то что человек заплатил за тариф

        // и +1 рефералл
        $this->assertEquals($assert_account_corporate->getTotalAvailableInvitesLimit(), $tariff->simulations_amount + 1 + 1);

        $this->assertEquals($assert_account_corporate->getActiveTariff()->slug, Tariff::SLUG_LITE);
        //Тест 3.1. Проверить, что при устаревании тарифного плана, после LiteFree у человека будет Free.
        $active_plan = $assert_account_corporate->getActiveTariffPlan();
        $active_plan->finished_at = (new DateTime())->format("Y-m-d H:i:s");
        $active_plan->save(false);
        UserService::tariffExpired();
        $assert_account_corporate->refresh();

        $active_plan = $assert_account_corporate->getActiveTariffPlan();
        $this->assertEquals($active_plan->tariff->slug, Tariff::SLUG_FREE);

        $this->assertEquals($assert_account_corporate->invites_limit, $active_plan->tariff->simulations_amount);

        // и +1 рефералл
        $this->assertEquals(
            $assert_account_corporate->getTotalAvailableInvitesLimit(),
            $active_plan->tariff->simulations_amount +1
        );
    }

    public function testPaymentSystem() {

        $this->deleteUsersByEmail([
            'test-corporate-phpunit-account@skiliks.com']);
        //Создаем корпоративного пользователя для тестов
        $user_corporate  = new YumUser('registration');
        $user_corporate->setAttributes(['password'=>'Skiliks123123', 'password_again'=>'Skiliks123123', 'agree_with_terms'=>'yes']);
        $profile_corporate  = new YumProfile('registration_corporate');
        $profile_corporate->setAttributes(['firstname'=>'Алексей', 'lastname'=>'Сафронов', 'email'=>'test-corporate-phpunit-account@skiliks.com']);
        $account_corporate = new UserAccountCorporate('corporate');
        $account_corporate->setAttributes(['industry_id'=>Industry::model()->findByAttributes(['label'=>'Другая'])->id]);
        $assert_result_corporate = UserService::createCorporateAccount($user_corporate, $profile_corporate, $account_corporate);
        $this->assertTrue($assert_result_corporate);

        //Активируем его
        $status_activation = YumUser::activate($profile_corporate->email, $user_corporate->activationKey);
        $this->assertInstanceOf('YumUser', $status_activation);
        /* @var $assert_profile_corporate YumProfile */
        $assert_profile_corporate = YumProfile::model()->findByAttributes(['email'=>'test-corporate-phpunit-account@skiliks.com']);

        $account = &$assert_profile_corporate->user->account_corporate;
        /* @var $tariffLiteFree Tariff */
        $tariffLiteFree = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_LITE_FREE]);

        // количество симуляций верное для только что активированного аккаунта?
        $this->assertEquals($tariffLiteFree->simulations_amount, $account->getTotalAvailableInvitesLimit());

        // ---

        /* @var $tariff Tariff */
        $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_FREE]);
        $account->setTariff($tariff, true);
        $active_tariff_plan = $account->getActiveTariffPlan();
        $before_tariff_plan_id = $active_tariff_plan->id;
        $active_tariff_plan->finished_at = (new DateTime())->format("Y-m-d H:i:s");
        $active_tariff_plan->save(false);

        UserService::tariffExpired();

        $after_plan = $account->getActiveTariffPlan();
        //Тест 3. Проверить, что при устаревании тарифного плана, после Free у человека будет Free.
        $this->assertNotEquals($before_tariff_plan_id, $after_plan->id);
        $this->assertEquals(Tariff::SLUG_FREE, $after_plan->tariff->slug);
        $this->assertEquals(0, $account->getTotalAvailableInvitesLimit());

        // ---
        //Тест 2. Проверить с Free тарифного плана нельзя перейти на LiteFree.
        //2.1. На уровне попапа
        //2.2. Если использовать setTariff()
        $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_LITE_FREE]);
        $this->assertFalse(UserService::isAllowOrderTariff($tariff, $account));

        // проверка ссылки для попапа
        //Тест 1. Проверить с Free тарифного плана можно перейти на больший.
        //1.1. На уровне попапа
        $action = UserService::getActionOnPopup($account, Tariff::SLUG_LITE);
        $this->assertEquals(['type'=>'link'], $action);

        $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_LITE]);
        $invoice = UserService::createFakeInvoiceForUnitTest($tariff, $account);
        //1.2. вызвать setTariff() в нутри метода completeInvoice
        $this->assertTrue($invoice->completeInvoice());

        $active_tariff = $account->getActiveTariffPlan();

        $this->assertEquals(Tariff::SLUG_LITE, $active_tariff->tariff->slug);

        $account->refresh();
        $this->assertEquals($tariff->simulations_amount, $account->getTotalAvailableInvitesLimit());

        // ---

        $action = UserService::getActionOnPopup($account, Tariff::SLUG_LITE);

        $this->assertEquals('extend-tariff-popup', $action['popup_class']);


        $action = UserService::getActionOnPopup($account, Tariff::SLUG_PROFESSIONAL);

        $this->assertEquals('tariff-replace-now-popup', $action['popup_class']);

        //Тест 7. Проверить случай перехода с Lite на PROFESSIONAL,при наличии активного тарифа, но пользователь выбрал "применить сейчас". Татифный план должен быть применён сразу.
        $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_PROFESSIONAL]);

        $invoice = UserService::createFakeInvoiceForUnitTest($tariff, $account);

        $this->assertTrue($invoice->completeInvoice());


        $active_tariff = $account->getActiveTariffPlan();

        $this->assertEquals(Tariff::SLUG_PROFESSIONAL, $active_tariff->tariff->slug);

        // ---

        $action = UserService::getActionOnPopup($account, Tariff::SLUG_STARTER);

        $this->assertEquals('downgrade-tariff-popup', $action['popup_class']);

        //Тест 6. Проверить случай перехода с Lite на Started,при наличии активного тарифа. Татифный план должен ставиться в очередь.
        $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_STARTER]);
        $invoice = UserService::createFakeInvoiceForUnitTest($tariff, $account);
        $this->assertTrue($invoice->completeInvoice());
        $account->refresh();
        $active_tariff = $account->getActiveTariffPlan();

        $this->assertEquals(Tariff::SLUG_PROFESSIONAL, $active_tariff->tariff->slug);
        $this->assertEquals(
            $account->getActiveTariffPlan()->tariff->simulations_amount,
            $account->getTotalAvailableInvitesLimit()
        );

        //Тест 5. Проверить случай перехода с Started на Lite. Татифный план должен ставиться в очередь.

        $pending_tariff = $account->getPendingTariffPlan();

        $this->assertEquals(Tariff::SLUG_STARTER, $pending_tariff->tariff->slug);

        // ---

        $action = UserService::getActionOnPopup($account, Tariff::SLUG_STARTER);

        $this->assertEquals('tariff-already-booked-popup', $action['popup_class']);
        //Тест 4. Проверить что LiteFree тарифный план нельзя продлить.
        $this->assertFalse(UserService::isAllowOrderTariff($tariff, $account));

    }

}