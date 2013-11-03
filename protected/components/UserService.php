<?php

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
     * Returns personal user statuses form
     * @return array
     */

    public static function getStatusesForm() {
        $statuses = [''=>'Выберите статус'];
        foreach (ProfessionalStatus::model()->findAll() as $status) {
            $statuses[$status->id] = Yii::t('site', $status->label);
        }
        return $statuses;
    }

    /**
     * Returns corporate user industries form
     * @return array
     */

    public static function getIndustriesForm() {
        $industries = [''=>'Выберите отрасль'];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = Yii::t('site', $industry->label);
        }
        return $industries;
    }

    /**
     * Sends user registration email.
     * @param $user
     * @return bool
     * @throws CException
     */

    public static function sendRegistrationEmail(YumUser $user)
    {

        Yii::app()->session->add("email", $user->profile->email);
        Yii::app()->session->add("user_id", $user->id);
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

    public static function createCorporateAccount(YumUser &$user, YumProfile &$profile, UserAccountCorporate &$account_corporate) {

        if(self::createUserAndProfile($user, $profile)
            && $account_corporate->validate(['industry_id'])
            && $user->register($user->username, $user->password, $profile)) {
            if(!$user->save()) { throw new Exception("User not save"); }
            $profile->user_id = $user->id;
            if(!$profile->save()) { throw new Exception("Profile not save"); }
            $account_corporate->user_id = $user->id;
            $account_corporate->default_invitation_mail_text = 'Вопросы относительно тестирования вы можете задать по адресу '.$profile->email.', куратор тестирования - '.$profile->firstname.' '. $profile->lastname .'.';
            $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE]);
            $account_corporate->setTariff($tariff, true);

            $account_corporate->invites_limit = Yii::app()->params['initialSimulationsAmount'];
            if(!$account_corporate->save()){
                throw new Exception("UserAccount not save");
            }

            UserService::logCorporateInviteMovementAdd(
                sprintf('Количество симуляций для нового аккаунта номер %s, емейл %s, задано равным %s по тарифному плану %s.',
                    $account_corporate->user_id, $profile->email, $account_corporate->getTotalAvailableInvitesLimit(), $tariff->label
                ),
                $account_corporate,
                $account_corporate->getTotalAvailableInvitesLimit()
            );
            return true;
        }
        return false;
    }

    public static function createPersonalAccount(YumUser &$user, YumProfile &$profile, UserAccountPersonal &$account_personal) {
        if(self::createUserAndProfile($user, $profile) && $account_personal->validate(['professional_status_id'])){
            $user->save();
            $profile->save();
            $account_personal->user_id = $user->id;
            $account_personal->save();
            return true;
        }
        return false;
    }

    public static function createUserAndProfile(YumUser &$user, YumProfile &$profile) {
        $user->setUserNameFromEmail($profile->email);
        $user->createtime = time();
        $user->lastvisit = time();
        $user->lastpasswordchange = time();
        $profile->timestamp = gmdate("Y-m-d H:i:s");
        return $user->validate(['password', 'password_again', 'agree_with_terms'])
               && $profile->validate(['firstname', 'lastname', 'email']);
    }

}


