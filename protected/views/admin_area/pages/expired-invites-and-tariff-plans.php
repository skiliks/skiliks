<?php

echo "time: ".date("Y-M-d H:i:s")."<br>";

echo '<hr/>';

if (0 == count($expiredInvites)) {
    echo "Ни одно приглашение не устарело. <br>";
}
foreach ($expiredInvites as $expiredInvite) {
    echo "Приглашение <strong>#".$expiredInvite->id."</strong> устарело. <br/>";
}

echo '<hr/>';

if (0 == count($expiredAccounts)) {
    echo "Ни один аккаунт не устарел. <br>";
}
foreach ($expiredAccounts as $expiredAccount) {
    echo "Аккаунт пользователя <strong>".$expiredAccount->user->profile->email."</strong> устарел. <br/>";
}

echo '<hr/>';

if (0 == count($expiredSoonAccounts)) {
    echo "Ни один аккаунт не должен устареть через 3 дня. <br>";
}

foreach ($expiredSoonAccounts as $expiredSoonAccount) {
    echo "Аккаунт пользователя <strong>".$expiredSoonAccount->user->profile->email."</strong> устарет через 3 дня. <br/>";
}