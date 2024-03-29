<?php

/**
 *
 */
class UserServiceUnitTest extends CDbTestCase
{
    use UnitTestBaseTrait;

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

        /*
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

        $invite->refresh();
        $this->assertEquals($invite->status, Invite::STATUS_PENDING);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 6);


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


        //Списываем 4 и проверяем что у нас 0 на обоих счетах
        $assert_account_corporate->changeInviteLimits(-4);
        $assert_account_corporate->refresh();
        $this->assertEquals($assert_account_corporate->invites_limit, 4);
        $invite2->refresh();
        $this->assertEquals($invite2->status, Invite::STATUS_PENDING);*/

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

    }

    public function testDebug(){
        $text = '12,5%';
        if(true) {
            if($text[strlen($text) - 1] === '%' && !in_array(',', str_split($text))){
                $text = str_replace('%', '', $text).',00%';
            }elseif($text[strlen($text) - 1] === '%' && strlen(explode(',', $text)[1]) === 2){
                var_dump($text);
                $text = str_replace('%', '', $text).'0%';
            }
        }

    }

}