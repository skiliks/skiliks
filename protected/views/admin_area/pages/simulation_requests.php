
<h1>
    Симуляция <?= $simulation->id ?>,
    <small><?= $simulation->start ?> ~ <?= $simulation->end ?></small>
</h1>

<?= $simulation->user->profile->firstname ?> <?=$simulation->user->profile->lastname ?>,
<?=$simulation->user->profile->email ?>

<br/>
    <?php if (null !== $simulation->invite): ?>
        <a href="/admin_area/invite/<?= $simulation->invite->id?>/site-logs">Смотреть логи приглашения</a>,
    <?php endif ?>
    <a href="/admin_area/simulation/<?= $simulation->id?>/site-logs">Смотреть логи симуляции</a>
<br/>
<br/>

<table class="table table-bordered" border="3px">
    <thead>
        <tr>
            <th>Реальное время (UID) <strong>Игровое время</strong> , URL</th>
            <th>Запрос</th>
            <th>Ответ</th>
        </tr>
    </thead>
    <?php foreach ($simulationLogs as $simulationLog) : ?>
        <tr style="height: 120px;">
            <td>
                <?= $simulationLog->real_time ?> (<?= $simulationLog->request_uid?>)
                <strong><?= $simulationLog->frontend_game_time ?></strong>,

                </br>
                <?= str_replace('/index.php/', '', $simulationLog->request_url) ?>
            </td>
            <td style="padding: 5px;">
                <div style="width: 500px; height: 120px; overflow: auto;">
                    <?= str_replace(',', ', ', $simulationLog->request_body) ?>
                </div>
            </td>
            <td style="padding: 5px;">
                <div style="width: 500px; height: 120px; overflow: auto;">
                    <?= str_replace(',', ', ', $simulationLog->response_body) ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>