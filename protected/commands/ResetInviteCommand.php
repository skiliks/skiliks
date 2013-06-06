<?php

class ResetInviteCommand extends CConsoleCommand
{
    public function actionIndex($email, $inviteId)
    {
        $profile = YumProfile::model()->findByAttributes(['email' => $email]);
        /** @var Invite $invite */
        $invite = Invite::model()->findByPk($inviteId);

        if (empty($profile)) {
            throw new LogicException('User with this email does not exist');
        } elseif (empty($invite)) {
            throw new LogicException('Invite does not exist');
        } elseif ($invite->receiver_id !== $profile->user_id) {
            throw new LogicException('Specified user is not invite owner');
        }

        $invite->status = Invite::STATUS_ACCEPTED;
        $invite->simulation_id = null;
        $result = $invite->save(false);

        echo $result ? 'Success' : 'Fail';
        return $result === true ? 0 : 1;
    }
}