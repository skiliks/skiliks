<?php
/**
 * Created by JetBrains PhpStorm.
 * User: PS
 * Date: 3/13/13
 * Time: 6:42 PM
 * To change this template use File | Settings | File Templates.
 */

class CleanUsersCommand extends CConsoleCommand
{
    public function actionIndex($timeAge = 604800) // 7 days
    {
        Yum::module()->trulyDelete = true;
        $oldUsers = YumUser::model()->findAll(
            'createtime + :age < UNIX_TIMESTAMP() AND status = :status',
            ['age' => $timeAge, 'status' => YumUser::STATUS_INACTIVE]
        );

        /** @var YumUser[] $oldUsers */
        foreach ($oldUsers as $user) {
            $user->delete();
        }

        echo 'Total removed: ' . count($oldUsers);
    }
}