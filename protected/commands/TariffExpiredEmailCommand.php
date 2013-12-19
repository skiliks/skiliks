<?php

class TariffExpiredEmailCommand extends CConsoleCommand {

    public function actionIndex(){

        // Accounts {
        $expiredAccounts = UserService::tariffExpiredInTreeDays();

        if (0 == count($expiredAccounts)) {
            echo "Ни один аккаунт не устареет.\n";
        }
        foreach ($expiredAccounts as $expiredAccount) {
            echo $expiredAccount->user->profile->email."\n";
        }
        // Accounts }
    }
}