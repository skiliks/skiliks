<?php
/**
 * initbaseusers
 */
class InitBaseUsersCommand
{
    public function init() {}

    /**
     * Code copied and fixed from YumInstallController->actionInstall()
     */
    public function run($args)
    {
        ini_set('memory_limit', '500M');

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

        $users = Yii::app()->params['initial_data']['users'];
        foreach ($users as $user) {
            echo "\n user {$user['username']}:";

            // get user, if user has been already initialized
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
            $profile->email     = $user['email'];

            // register user (init user object, validate, save) {
            if (null === $yumUser) {
                echo " init ";
                $yumUser = new YumUser();
                if ($yumUser->register($user['username'], $user['password'], $profile)) {
                    echo " => registered";
                } else {
                    echo " => NOT registered";
                }
            } else {
                echo " exists";
            }
            // register user (init user object, validate, save) }

            // activate user {
            $actStatus = $yumUser->activate($user['email'], $yumUser->activationKey);

            if ($actStatus instanceof YumUser || -1 == $actStatus) {
                echo " => activated";
            } else {
                echo " => NOT activated";
            }
            // activate user }

            $permissionSrartDevMode = YumPermission::model()->findByAttributes([
                'type'         => 'user',
                'principal_id' => $yumUser->id,
                'action'       => $actionSrartDevMode->id
            ]);

            if (isset($user['is_admin']) && 1 == $user['is_admin']) {
                if (null === $permissionSrartDevMode) {
                    $permissionSrartDevMode = new YumPermission();
                    $permissionSrartDevMode->principal_id   = $yumUser->id;
                    $permissionSrartDevMode->subordinate_id = $yumUser->id;
                    $permissionSrartDevMode->type           = 'user';
                    $permissionSrartDevMode->action         = $actionSrartDevMode->id;
                    $permissionSrartDevMode->template       = true;
                    $permissionSrartDevMode->save();

                    echo " => permission to start sim in dev mode granted";
                }
            } else {
                // remove uesr permission to SrartDevMode
                if (null !== $permissionSrartDevMode) {
                    $permissionSrartDevMode->delete();

                    echo " => permission to start sim in dev mode removed";
                }
            }
        }

        echo "\n\n InitBaseUsers complete. \n";
    }
}
