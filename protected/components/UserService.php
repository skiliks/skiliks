<?php



/**
 * Сервис по работе с пользователями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserService {

    const CAN_START_SIMULATION_IN_DEV_MODE = 'start_dev_mode';

    const MODE_PROMO     = 'promo';
    const MODE_DEVELOPER = 'developer';

    /**
     * Получить список режимов запуска симуляции доступных пользователю: {promo, developer}
     * @param int $uid 
     * @return array
     */
    public static function getModes($user)
    {
        $modes = [];
        $modes[1] = self::MODE_PROMO;

        if ($user->can(self::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $modes[2] = self::MODE_DEVELOPER;
        }
        
        return $modes;
    }
    
    public static function addGroupToUser($uid, $gid) {
        $userGroups = new UserGroup();
        $userGroups->uid = $uid;
        $userGroups->gid = $gid;
        $userGroups->insert();
    }
    
    /**
     * Проверяет является ли пользователь членом заданной группы
     * @param int $uid
     * @param int $gid
     * @return bool
     */
    public static function isMemberOfGroup($uid, $gid) {
        return (1 === (int)UserGroup::model()->byUser($uid)->byGroup($gid)->count());
    }
    
    /**
     * @param string $email
     * @param string $password
     * @param string $passwordAgain
     * 
     * @return string|boolean
     */
    public static function validateNewUserData($email, $password, $passwordAgain)
    {
        if (Users::model()->byEmail($email)->isActive()->find()) {
            return sprintf("Пользователь с емейлом %s уже существует", $email);
        }
        
        if ($password != $passwordAgain) {    
            return 'Введенные пароли не совпадают';
        }
        
        return true;
    }
    
    public static function registerUser($email, $password)
    {
        $user = new Users();
        $user->password    = $user->encryptPassword($password);
        $user->email       = $email;
        $user->is_active   = 1;
        try {
            $user->insert();
        } catch (Exception $e) {
            Yii::log($e->getMessage());
            Yii::log($e->getTraceAsString());
            return null;
        }

        $activationCode            = $user->generateActivationCode();
        $usersActivationCode       = new UsersActivationCode();
        $usersActivationCode->uid  = $user->id;
        $usersActivationCode->code = $user->activationCode;
        $usersActivationCode->insert();

        // Добавить группы пользователей
        UserService::addGroupToUser($user->id, 1);
        
        return $user;
    }

    public static function addUserSubscription($email)
    {
        $response = ['result'  => 0];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] =  Yii::t('site', "Invalid email - '{email}'!", ['{email}' => $email]);
        } elseif (EmailsSub::model()->findByEmail($email)) {
            $response['message'] =  Yii::t('site', "Email - {email} has been already added before!", ['{email}' => $email]);
        } else {
            $subscription = new EmailsSub();
            $subscription->email = $email;
            $subscription->save();

            $response['result'] =  1;
            $response['message'] =  Yii::t('site', 'Email {email} has been successfully added!', ['{email}' => $email]);
        }

        return $response;
    }
}


