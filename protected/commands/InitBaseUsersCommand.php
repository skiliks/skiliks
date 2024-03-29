<?php
/**
 * Инициализация пользователей для всех участников проекта
 * Список пользователей берётся из /protected/config/base.php, массив ['params']['initial_data']['users']
 */
class InitBaseUsersCommand
{
    public function init() {}

    /**
     * Code copied and fixed from YumInstallController->actionInstall()
     */
    public function run($forceDelete = false)
    {
        //Удаление всех аккаунтов
        if($forceDelete) {
            YumUser::model()->deleteAll();
            YumProfile::model()->deleteAll();
            UserAccountCorporate::model()->deleteAll();
            UserAccountPersonal::model()->deleteAll();
        }
        ini_set('memory_limit', '900M');

        echo "\n Start InitBaseUsers \n";

        $actionSrartDevMode = YumAction::model()->findByAttributes([
            'title' => 'start_dev_mode'
        ]);

        if (null === $actionSrartDevMode) {
            $actionSrartDevMode = new YumAction();
            $actionSrartDevMode->title   = 'start_dev_mode';
            $actionSrartDevMode->comment = 'Is user can start simulation in developers mode.';
            $actionSrartDevMode->save();
        }

        $adminRole = YumRole::model()->findByAttributes(['title' => 'Админ']);
        $superAdminRole = YumRole::model()->findByAttributes(['title' => 'СуперАдмин']);

        $users = Yii::app()->params['initial_data']['users'];
        foreach ($users as $user) {
            echo "\n user {$user['username']}:";

            // get user, if user has been already initialized
            /* @var $yumUser YumUser */
            $yumUser = YumUser::model()->findByAttributes([
                'username' => $user['username']
            ]);

            $profile = YumProfile::model()->findByAttributes([
                'firstname' => $user['username']
            ]);

            // get profile, if profile has been already initialized
            if (null === $profile) {
                $profile = new YumProfile();
            }

            $profile->firstname = $user['username'];
            $profile->lastname = 'L.N.';
            $profile->email     = strtolower($user['email']);

            // register user (init user object, validate, save) {
            if (null === $yumUser) {
                echo " init ";
                $yumUser = new YumUser();
                $yumUser->agree_with_terms = YumUser::AGREEMENT_MADE;
                $yumUser->is_admin = $user['is_admin'];
                if ($yumUser->register($user['username'], $user['password'], $profile)) {
                    echo " => registered";
                    $accountCorporate = new UserAccountCorporate();
                    $industry = Industry::model()->findByAttributes(['label'=>'Другая']);
                    $accountCorporate->user_id = $yumUser->id;
                    $accountCorporate->industry_id = $industry->id;
                    if(false == $accountCorporate->save(['user_id, industry_id'])){
                        throw new Exception(print_r($accountCorporate->getErrors()));
                    }
                } else {
                    print_r($yumUser->getErrors());
                    print_r($user);
                    throw new Exception('User ' . $user['username'] . ' not registered');
                }
            } else {
                echo " exists";
            }
            // register user (init user object, validate, save) }

            // activate user {
            $actStatus = $yumUser->activate(strtolower($user['email']), $yumUser->activationKey);

            if ($actStatus instanceof YumUser || -1 == $actStatus) {
                echo " => activated";
            } else {
                echo " => NOT activated";
            }
            // activate user }

            // setRole
            if (in_array($profile->email, ['slavka@skiliks.com', 'tony@skiliks.com'])) {
                // везёт же некоторым ;) - прав много
                UserService::setRole($profile->user, $superAdminRole);
            } else {
                UserService::setRole($profile->user, $adminRole);
            }
        }

        echo "\n\n InitBaseUsers complete. \n";
    }
}
