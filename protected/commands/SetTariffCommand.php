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
    public function actionIndex($email, $tariff = null, $period = 0)
    {
        $profile = YumProfile::model()->findByAttributes(['email' => $email]);

        if (empty($profile)) {
            throw new LogicException('User with this email does not exist');
        } elseif (!$profile->user->isCorporate()) {
            throw new LogicException('Specified user is not a corporate one');
        }

        /** @var UserAccountCorporate $account */
        $account = $profile->user->account_corporate;

        if ($tariff) {
            $tariff = Tariff::model()->findByAttributes(['slug' => $tariff]);
            $account->tariff_activated_at = date('Y-m-d H:i:s');
            $account->tariff_id = $tariff->id;
        }

        if ($period) {
            $date = new DateTime($account->tariff_expired_at);
            $date->add(new DateInterval('P' . (int)$period . 'D'));

            $account->tariff_expired_at = $date->format('Y-m-d H:i:s');
        }

        $result = $account->save(false);

        echo $result ? 'Success' : 'Fail';
        return $result === true ? 0 : 1;
    }
}