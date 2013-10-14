<?php

class SetInviteCountCommand extends CConsoleCommand
{
    /**
     * Увеличивает или уменьшает кол-во доступных приглашений корпоративному пользователю
     *
     * @param $email
     * @param int $add
     * @param int $remove
     * @return int
     * @throws LogicException
     */
    public function actionIndex($email, $add = 0, $remove = 0)
    {
        $profile = YumProfile::model()->findByAttributes(['email' => strtolower($email)]);

        if (empty($profile)) {
            throw new LogicException('User with this email does not exist');
        } elseif (!$profile->user->isCorporate()) {
            throw new LogicException('Specified user is not a corporate one');
        }

        /** @var UserAccountCorporate $account */
        $account = $profile->user->account_corporate;

        $initValue = $account->getTotalAvailableInvitesLimit();

        $account->invites_limit += $add;
        $account->invites_limit -= $remove;
        $result = $account->save(false);

        UserService::logCorporateInviteMovementAdd(
            sprintf('Количество доступных симуляций установлено в Х консольной командой, из них за рефераллов Х.',
                $account->invites_limit, $account->referrals_invite_limit),
            $account,
            $initValue
        );

        echo $result ? 'Success' : 'Fail';
        return $result === true ? 0 : 1;
    }
}