<?php

class ResetInviteCommand extends CConsoleCommand
{
    /**
     * Сбрасывает инвайт в состояние принятого.
     * Необходимо указать email пользователя получившего инвайт и его айди.
     * При этом если симуляция была начата по этому инвайту, она будет отвязана.
     *
     * @param $email
     * @param $inviteId
     * @return int
     * @throws LogicException
     */
    public function actionIndex($email, $inviteId)
    {
        $profile = YumProfile::model()->findByAttributes(['email' => strtolower($email)]);
        /** @var Invite $invite */
        $invite = Invite::model()->findByPk($inviteId);

        if (empty($profile)) {
            throw new LogicException('User with this email does not exist');
        } elseif (empty($invite)) {
            throw new LogicException('Invite does not exist');
        } elseif ($invite->receiver_id !== $profile->user_id) {
            throw new LogicException('Specified user is not invite owner');
        }

        $result = $invite->resetInvite();

        echo $result ? 'Success' : 'Fail';
        return $result === true ? 0 : 1;
    }
}