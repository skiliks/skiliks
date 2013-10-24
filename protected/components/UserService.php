<?php



/**
 * Сервис по работе с пользователями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserService {

    const CAN_START_SIMULATION_IN_DEV_MODE = 'start_dev_mode';
    const CAN_START_FULL_SIMULATION = 'run_full_simulation';

    public static $developersEmails = [
        "'r.kilimov@gmail.com'",
        "'andrey@kostenko.name'",
        "'personal@kostenko.name'",
        "'a.levina@gmail.com'",
        "'gorina.mv@gmail.com'",
        "'v.logunov@yahoo.com'",
        "'nikoolin@ukr.net'",
        "'leah.levina@gmail.com'",
        "'lea.skiliks@gmail.com'",
        "'andrey3@kostenko.name'",
        "'skiltests@yandex.ru'",
        "'didmytime@bk.ru'",
        "'gva08@yandex.ru'",
        "'tony_acm@ukr.net'",
        "'tony_perfectus@mail.ru'",
        "'N_ninok1985@mail.ru'",
        "'tony.pryanichnikov@gmail.com'",
        "'svetaswork@gmail.com'",
        "'tatyana_pryan@mail.ru'",
    ];

    /**
     * Получить список режимов запуска симуляции доступных пользователю: {promo, developer}
     * @param int $uid 
     * @return array
     */
    public static function getModes($user)
    {
        $modes = [];
        $modes[1] = Simulation::MODE_PROMO_LABEL;

        if ($user->can(self::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $modes[2] = Simulation::MODE_DEVELOPER_LABEL;
        }
        
        return $modes;
    }
    
    public static function addUserSubscription($email)
    {
        $response = ['result'  => 0];

        if(empty($email)) {
                $response['message'] =  "Enter your email address";
        }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] =  "Email entered incorrectly";
        } elseif (EmailsSub::model()->findByEmail($email)) {
            $response['message'] =  "Email - ${email} has been already added before!";
        } else {
            $subscription = new EmailsSub();
            $subscription->email = strtolower($email);
            $subscription->save();

            $response['result'] =  1;
            $response['message'] =  "Email ${email} has been successfully added!";
        }

        return $response;
    }

    public static function isCorporateEmail($email)
    {
        $domain = substr($email, strpos($email, '@') + 1);

        $counter = FreeEmailProvider::model()->countByAttributes([
            'domain' => $domain
        ]);

        if(0 != $counter) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Добавляет лог о состоянии баланса инвайтов по сккаунту
     *
     * @param string, $action
     * @param UserAccountCorporate $account
     * @param $amountBeforeTransaction
     * @param $isAdd
     * @param null $comment
     */
    public static function logCorporateInviteMovementAdd($message, $account, $amountBeforeTransaction, $comment = null )
    {
        if (null == $account) {
            return false;
        }

        if (false === $account instanceof UserAccountCorporate) {
            return false;
        }

        $log = new LogAccountInvite();
        $log->message = $message;
        $log->user_id = $account->user_id;
        $log->direction = ($account->getTotalAvailableInvitesLimit() > $amountBeforeTransaction) ? 'увеличено' : 'уменьшено';
        $log->limit_after_transaction = $account->invites_limit;
        $log->invites_limit_referrals = $account->referrals_invite_limit;
        $log->amount = $amountBeforeTransaction;
        $log->date = date('Y-m-d H:i:s');
        if(false == (Yii::app() instanceof CConsoleApplication) && Yii::app()->user->data()->id !== null) {
            try {
                $log->comment = $comment.'. Инициатор, пользователь '.Yii::app()->user->data()->id.', '.
                    Yii::app()->user->data()->profile->firstname.' '.Yii::app()->user->data()->profile->lastname.'.';
            } catch (Exception $e) {
                $log->comment = $comment;
            }
        } else {
            $log->comment = $comment;
        }

        $log->save(false);
    }

    /**
     * @param YumUser $user
     */
    public static function assignAllNotAssignedUserInvites(YumUser $user)
    {
        $invites = Invite::model()->findAllByAttributes([
            'email' => strtolower($user->profile->email)
        ]);

        foreach ($invites as $invite) {
            if (null !== $invite->receiver_id) {
                continue;
            }
            $invite->receiver_id = $user->id;
            $invite->receiverUser = $user;
            $invite->save(false);
        }
    }

    /**
     * Returns new objects for registration form, also returns All Industries and statuses
     */

    public static function getRegistrationForm() {
        // generating
        $returnData = [];
        $returnData['user']             = new YumUser('registration');
        $returnData['profile']          = new YumProfile();
        $returnData['accountCorporate'] = new UserAccountCorporate();
        $returnData['accountPersonal']  = new UserAccountPersonal();
        $returnData['industries']       = self::getIndustriesForm();
        $returnData['statuses']         = self::getStatusesForm();
        $returnData['account_type']     = 'corporate';
        return $returnData;
    }

    public static function getStatusesForm() {
        $statuses = [''=>'Выберите статус'];
        foreach (ProfessionalStatus::model()->findAll() as $status) {
            $statuses[$status->id] = Yii::t('site', $status->label);
        }
        return $statuses;
    }

    public static function getIndustriesForm() {
        $industries = [''=>'Выберите отрасль'];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = Yii::t('site', $industry->label);
        }
        return $industries;
    }

    public static function completeRegistrationFromForm($UserAccountCorporateData, $UserAccountPersonalData,
                                                        $YumProfileData, $YumUserData, $account_type) {
        // preparing if userEmailExists
        $emailIsExistAndNotActivated = false;

        switch($account_type) {
            case 'corporate' :
                $returnData = UserService::registerCorporateUser($YumProfileData['firstname'], $YumProfileData['lastname'],
                    $YumProfileData['email'], $YumUserData['password'], $YumUserData['password_again'],
                    $YumUserData['agree_with_terms'], $UserAccountCorporateData['industry_id']);
                $returnData['accountPersonal'] = new UserAccountPersonal();
                break;

            case 'personal' :
                $returnData = UserService::registerPersonalUser($YumProfileData['firstname'], $YumProfileData['lastname'],
                    $YumProfileData['email'], $YumUserData['password'], $YumUserData['password_again'],
                    $YumUserData['agree_with_terms'], $UserAccountPersonalData['professional_status_id']);
                $returnData['accountCorporate'] = new UserAccountCorporate();
                break;
        }

        $returnData['account_type'] = $account_type;
        return $returnData;
    }

    public static function registerCorporateUser($firstname = null, $lastname = null, $email = null,
                                                 $password = null, $password_again = null, $agree_with_terms = null,
                                                 $industry_id = null) {

        $returnData['user']             = $user             = new YumUser('registration');
        $returnData['profile']          = $profile          = new YumProfile('registration_corporate');
        $returnData['accountCorporate'] = $accountCorporate = new UserAccountCorporate();
        $returnData['emailIsExistAndNotActivated'] = false;

        $profile->scenario = 'registration_corporate';
        $accountCorporate->scenario = 'corporate';

        self::prepareForRegistration( $profile, $user, $firstname, $lastname, $email, $password, $password_again,
                                      $agree_with_terms);

        $accountCorporate->industry_id = $industry_id;
        $accountValid = $accountCorporate->validate(['industry_id']);

        if(self::validateUserForRegistration($user, $profile, $returnData['emailIsExistAndNotActivated'])
            && $accountValid) {

            $is_success_registration = $user->register($user->username, $user->password, $profile);

            if ($is_success_registration) {
                $profile->user_id = $user->id;

                if(false === $profile->save()) {
                    throw new Exception("Profile not saved!");
                }

                $accountCorporate->user_id = $user->id;
                $accountCorporate->default_invitation_mail_text = 'Вопросы относительно тестирования вы можете задать по адресу '.$profile->email.', куратор тестирования - '.$profile->firstname.' '. $profile->lastname .'.';
                $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE]);
                $accountCorporate->setTariff($tariff, true);

                $accountCorporate->invites_limit = Yii::app()->params['initialSimulationsAmount'];
                $accountCorporate->save();

                UserService::logCorporateInviteMovementAdd(
                    sprintf('Количество симуляций для нового аккаунта номер %s, емейл %s, задано равным %s по тарифному плану %s.',
                        $accountCorporate->user_id, $profile->email, $accountCorporate->getTotalAvailableInvitesLimit(), $tariff->label
                    ),
                    $accountCorporate,
                    $accountCorporate->getTotalAvailableInvitesLimit()
                );

                if(false === $accountCorporate->save(true, ['user_id','default_invitation_mail_text','industry_id'])){
                    throw new Exception("Corporate account not saved!");
                }

                self::completeRegistration($user, $profile);
            }
        }
        return $returnData;
    }

    public static function registerPersonalUser($firstname = null, $lastname = null, $email = null,
                                                $password = null, $password_again = null, $agree_with_terms = null,
                                                $professional_status_id = null) {

        $returnData['user']             = $user             = new YumUser('registration');
        $returnData['profile']          = $profile          = new YumProfile('registration_corporate');
        $returnData['accountPersonal']  = $accountPersonal  = new UserAccountPersonal();
        $returnData['emailIsExistAndNotActivated'] = false;

        $profile->scenario = 'registration';
        $accountPersonal->scenario = 'personal';

        self::prepareForRegistration( $profile, $user, $firstname, $lastname, $email, $password, $password_again,
            $agree_with_terms);

        $accountPersonal->industry_id = $professional_status_id;
        $accountValid = $accountPersonal->validate(['professional_status_id']);

        if(self::validateUserForRegistration($user, $profile, $returnData['emailIsExistAndNotActivated'])
            && $accountValid ) {

            $is_success_registration = $user->register($user->username, $user->password, $profile);

            if ($is_success_registration) {
                $profile->user_id = $user->id;

                if(false === $profile->save()) {
                    throw new Exception("Profile not saved!");
                }

                $accountPersonal->user_id = $user->id;
                if (false === $accountPersonal->save(true, ['user_id', 'professional_status_id'])) {
                    throw new Exception("Personal account not saved!");
                }

                self::completeRegistration($user, $profile);

            }
        }

        return $returnData;
    }

    private static function completeRegistration($user, $profile) {
        self::sendRegistrationEmail($user);
        Yii::app()->session->add("email", $profile->email);
        Yii::app()->session->add("user_id", $profile->user_id);
        Yii::app()->getController()->redirect(['afterRegistration']);
    }

    private static function prepareForRegistration(&$profile, &$user, $firstname = null, $lastname = null, $email = null,
                                                   $password = null, $password_again = null, $agree_with_terms = null) {

        self::prepareProfile($profile, $firstname, $lastname, $email);
        self::prepareUser($user, $password, $password_again, $agree_with_terms, $profile->email);

    }

    private static function prepareProfile(&$profile, $firstname, $lastname, $email) {
        $profile->firstname = $firstname;
        $profile->lastname  = $lastname;
        $profile->email     = $email;
        $profile->timestamp = gmdate("Y-m-d H:i:s");
    }

    private static function prepareUser(&$user, $password, $password_again, $agree_with_terms, $email) {
        $user->password         = $password;
        $user->password_again   = $password_again;
        $user->agree_with_terms = $agree_with_terms;
        $user->setUserNameFromEmail($email);
        $user->createtime = time();
        $user->lastvisit = time();
        $user->lastpasswordchange = time();
    }

    private static function validateUserForRegistration(&$user, &$profile, &$emailIsExistAndNotActivated) {

        $userValid    = $user->validate(['password', 'password_again', 'agree_with_terms']);
        $profileValid = $profile->validate(['firstname', 'lastname', 'email']);

        $emailIsExistAndNotActivated = YumProfile::model()->emailIsNotActiveValidationStatic($profile->email);
        if($emailIsExistAndNotActivated) {
            $profile->clearErrors();
        }

        $isUserBanned = YumProfile::model()->isAccountBannedStatic($profile->email);

        /**
         * if User is banned we need to replace email error with banned error
         */

        if($isUserBanned) {
            $emailIsExistAndNotActivated = $isUserBanned;
        }

        return ($userValid && $profileValid) ? true : false;

    }

    private static function sendRegistrationEmail($user)
    {
        if (!isset($user->profile->email)) {
            throw new CException(Yum::t('Email is not set when trying to send Registration Email'));
        }
        $activation_url = $user->getActivationUrl();

        $body = Yii::app()->getController()->renderPartial('//global_partials/mails/registration', ['link' => $activation_url], true);

        $mail = array(
            'from' => Yum::module('registration')->registrationEmail,
            'to' => $user->profile->email,
            'subject' => 'Активация на сайте skiliks.com',
            'body' => $body,
            'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mailtopangela.png',
                    'cid'      => 'mail-top-angela',
                    'name'     => 'mailtopangela',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mailanglabtm.png',
                    'cid'      => 'mail-bottom-angela',
                    'name'     => 'mailbottomangela',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
                    'cid'      => 'mail-bottom',
                    'name'     => 'mailbottom',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],
            ],
        );
        $sent = MailHelper::addMailToQueue($mail);

        return $sent;
    }

}


