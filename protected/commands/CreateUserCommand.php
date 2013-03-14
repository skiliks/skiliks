<?php
/**
 * Created admin user
 *
 * php protected/yiic.php createuser --email=xxx --password=xxx
 */

class CreateUserCommand extends CConsoleCommand
{
    public function actionIndex($login, $password, $isAdmin="true")
    {
        /*$user = YumUser::model()->findByAttributes(['email' => $login]);
        $action = 'updated';
        if ($user === null) {
            $user = new YumUser();
            $user->email = $login;
            $action = 'created';
        }
        $user->is_active = true;
        $user->password = md5($password);
        $user->save();
        echo "User $login successfully $action.";

        $group = Group::model()->findByAttributes(['name' => Simulation::MODE_DEVELOPER_LABEL]);
        $userGroup = UserGroup::model()->findByAttributes(['uid' => $user->primaryKey, 'gid' => $group->primaryKey]);
        if ($userGroup === null) {
            $userGroup = new UserGroup();
            $userGroup->uid = $user->primaryKey;
            $userGroup->gid = $group->primaryKey;
            $userGroup->save();

            echo "\nPromo access granted to $login.";
        }

        if ($isAdmin == 'true') {
            $group = Group::model()->findByAttributes(['name' => 'developer']);
            $userGroup = UserGroup::model()->findByAttributes(['uid' => $user->primaryKey, 'gid' => $group->primaryKey]);
            if ($userGroup === null) {
                $userGroup = new UserGroup();
                $userGroup->uid = $user->primaryKey;
                $userGroup->gid = $group->primaryKey;
                $userGroup->save();

                echo "\nAdmin access granted to $login.";
            }
        }

        echo "\n";*/
    }
}