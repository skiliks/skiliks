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

        $actionFullSim = YumAction::model()->findByAttributes([
            'title' => 'run_full_simulation'
        ]);

        if (null === $actionFullSim) {
            $actionFullSim = new YumAction();
            $actionFullSim->title   = 'run_full_simulation';
            $actionFullSim->comment = 'Is user can start full simulation.';
            $actionFullSim->save();
        }

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

            $actions = YumAction::model()->findAll();
            foreach ($actions as $action) {
                $permission = YumPermission::model()->findByAttributes([
                    'type'         => 'user',
                    'principal_id' => $yumUser->id,
                    'action'       => $action->id
                ]);

                if (isset($user['is_admin']) && 1 == $user['is_admin']) {
                    if (null === $permission) {
                        $permission = new YumPermission();
                        $permission->principal_id   = $yumUser->id;
                        $permission->subordinate_id = $yumUser->id;
                        $permission->type           = 'user';
                        $permission->action         = $action->id;
                        $permission->template       = true;
                        $permission->save();

                        echo " => permission '{$action->title}' granted";
                    }
                } else {
                    // remove user permission
                    if (null !== $permission) {
                        $permission->delete();

                        echo " => permission '{$action->title}' removed";
                    }
                }
            }
        }

        echo "\n\n InitBaseUsers complete. \n";
    }
}
