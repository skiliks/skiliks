<?php

class SetInviteCountCommand extends CConsoleCommand
{
    public function actionIndex($email, $add = 0, $remove = 0)
    {
        $profile = YumProfile::model()->findByAttributes(['email' => $email]);

        if (empty($profile)) {
            throw new LogicException('User with this email does not exist');
        } elseif (!$profile->user->isCorporate()) {
            throw new LogicException('Specified user is not a corporate one');
        }

        /** @var UserAccountCorporate $account */
        $account = $profile->user->account_corporate;
        $account->invites_limit += $add;
        $account->invites_limit -= $remove;
        $result = $account->save(false);

        echo $result ? 'Success' : 'Fail';
        return $result === true ? 0 : 1;
    }
}