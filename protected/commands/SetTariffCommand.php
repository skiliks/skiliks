<?php

class SetTariffCommand extends CConsoleCommand
{
    /**
     * Устанавливает указанный тариф корпоративному пользователю и/или продлевает на указанное кол-во дней
     *
     * @param $email
     * @param null $tariff
     * @param int $period
     * @return int
     * @throws LogicException
     */
    public function actionIndex($email, $tariff = null)
    {
        $profile = YumProfile::model()->findByAttributes(['email' => strtolower($email)]);

        if (empty($profile)) {
            throw new LogicException('User with this email does not exist');
        } elseif (!$profile->user->isCorporate()) {
            throw new LogicException('Specified user is not a corporate one');
        }

        /** @var UserAccountCorporate $account */
        $account = $profile->user->account_corporate;

        if ($tariff) {
            $tariff = Tariff::model()->findByAttributes(['slug' => $tariff]);
        }

        $initValue = $account->getTotalAvailableInvitesLimit();


        $account->setTariff($tariff);
        $result = $account->save(false);

        UserService::logCorporateInviteMovementAdd(
            sprintf('Тарифный план сменён на %s консольной командой. Количество доступных симуляций установлено в Х из них за рефераллов Х.',
                $tariff->label, $account->getTotalAvailableInvitesLimit(), $account->referrals_invite_limit),
            $account,
            $initValue
        );

        echo $result ? 'Success' : 'Fail';
        return $result === true ? 0 : 1;
    }
}