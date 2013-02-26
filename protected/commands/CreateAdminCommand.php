<?php
/**
 * Created admin user
 */

class CreateAdminCommand extends CConsoleCommand {
    public function init()
    {

    }

    public function actionIndex($email, $password)
    {
        $user = Users::model()->findByAttributes(['email' => $email]);
        if ($user === null) {
            $user = new Users();
            $user->email=$email;
        }
        $user->password=md5($password);
        $user->save();
        $group = Group::model()->findByAttributes(['name' => 'developer']);
        $userGroup = UserGroup::model()->findByAttributes(['uid' => $user->primaryKey, 'gid' => $group->primaryKey]);
        if ($userGroup === null) {
            $userGroup = new UserGroup();
            $userGroup->uid = $user->primaryKey;
            $userGroup->gid = $group->primaryKey;
            $userGroup->save();
        }
    }
}