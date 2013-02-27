<?php
/**
 * Created admin user
 *
 * php protected/yiic.php createuser --email=xxx --password=xxx
 */

class CreateUserCommand extends CConsoleCommand
{
    public function actionIndex($email, $password, $isAdmin="true")
    {
        $user = Users::model()->findByAttributes(['email' => $email]);
        $action = 'updated';
        if ($user === null) {
            $user = new Users();
            $user->email = $email;
            $action = 'created';
        }
        $user->is_active = true;
        $user->password = md5($password);
        $user->save();
        echo "User $email successfully $action.";

        $group = Group::model()->findByAttributes(['name' => 'promo']);
        $userGroup = UserGroup::model()->findByAttributes(['uid' => $user->primaryKey, 'gid' => $group->primaryKey]);
        if ($userGroup === null) {
            $userGroup = new UserGroup();
            $userGroup->uid = $user->primaryKey;
            $userGroup->gid = $group->primaryKey;
            $userGroup->save();

            echo "\nPromo access granted to $email.";
        }

        if ($isAdmin == 'true') {
            $group = Group::model()->findByAttributes(['name' => 'developer']);
            $userGroup = UserGroup::model()->findByAttributes(['uid' => $user->primaryKey, 'gid' => $group->primaryKey]);
            if ($userGroup === null) {
                $userGroup = new UserGroup();
                $userGroup->uid = $user->primaryKey;
                $userGroup->gid = $group->primaryKey;
                $userGroup->save();

                echo "\nAdmin access granted to $email.";
            }
        }

        echo "\n";
    }
}