<h1>
    Симуляция <?= $simulation->id ?>
</h1>

<br/>
<?php if (null !== $simulation->invite): ?>
    <a class="btn btn-info" href="/admin_area/invite/<?= $simulation->invite->id?>/site-logs">Перейти к Приглашению => </a>
    &nbsp;
    &nbsp;
    &nbsp;
<?php endif ?>
<a class="btn btn-info" href="/admin_area/simulation/<?= $simulation->id?>/site-logs">Перейти к Симуляции => </a>
<br/>
<br/>
<table class="table table-hover table-bordered">
    <tr>
        <td>ФИО / email</td>
        <td><?= (empty($simulation->user))?'Аноним':$simulation->user->profile->firstname ?> <?= (empty($simulation->user))?'Аноним':$simulation->user->profile->lastname ?> /
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
    <?php if (null !== $simulation->invite) : ?>
        <tr><td>Оценка</td><td> <?= (null !== $simulation->invite->getOverall()) ? $simulation->invite->getOverall() : '-'; ?></td></tr>
        <tr><td>Процентиль</td><td> <?= (null !== $simulation->invite->getPercentile()) ? $simulation->invite->getPercentile() : '-'; ?></td></tr>
    <?php endif; ?>
</table>