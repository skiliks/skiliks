<?php /* @var SiteLogAuthorization[] $logs */ ?>
ВНИМАНИЕ!
Обнаружена попытка подобрать пароль к аккаунту пользователя <?= $logs[0]->login ?>.
Лог подбора пароля:
<table>
    <tr>
        <th>Дата</th>
        <th>IP</th>
        <th>Пароль</th>
    </tr>
    <?php foreach($logs as $log) : ?>
    <tr>
        <td><?= $log->date ?></td>
        <td><?= $log->ip ?></td>
        <td><?= $log->password ?></td>
    </tr>
    <?php endforeach ?>
</table>
