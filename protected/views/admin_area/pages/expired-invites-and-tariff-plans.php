<?php

echo "time: ".date("Y-m-d H:i:s")."<br>";

if (0 == count($expiredInvites)) {
    echo "Ни одно приглашение не устарело. <br>";
}
foreach ($expiredInvites as $expiredInvite) {
    echo "Приглашение #".$expiredInvite->id." устарело. <br>";
}

if (0 == count($expiredAccounts)) {
    echo "Ни один аккаунт не устарел. <br>";
}
foreach ($expiredAccounts as $expiredAccount) {
    echo "Аккаунт пользователя ".$expiredAccount->user->profile->email." устарел. <br>";
}
