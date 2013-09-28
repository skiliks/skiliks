<h1>
    Симуляция <?= $simulation->id ?>
</h1>

<br/>
<?php if (null !== $simulation->invite): ?>
    <a href="/admin_area/invite/<?= $simulation->invite->id?>/site-logs">Смотреть логи приглашения</a>,
<?php endif ?>
<a href="/admin_area/simulation/<?= $simulation->id?>/site-logs">Смотреть логи симуляции</a>
<br/>
<table class="table table-hover table-bordered">
    <tr>
        <td>ФИО / email</td>
        <td><?= (empty($simulation->user))?'Аноним':$simulation->user->profile->firstname ?> <?=$simulation->user->profile->lastname ?> /
            <?= (empty($simulation->user))?'Аноним':$simulation->user->profile->email ?></td>
    </tr>
    <tr>
        <td>Start time ~ end time</td>
        <td><?= $simulation->start ?> ~ <?= ($simulation->end === null)?'нет данных':$simulation->end ?></td>
    </tr>
    <tr>
        <td>User Agent</td>
        <td><?= $simulation->user_agent ?></td>
    </tr>
    <tr>
        <td>Screen resolution</td>
        <td><?= $simulation->screen_resolution ?></td>
    </tr>
    <tr>
        <td>Window resolution</td>
        <td><?= $simulation->window_resolution ?></td>
    </tr>
    <tr>
        <td>IPv4</td>
        <td><?= $simulation->ipv4 ?></td>
    </tr>
</table>